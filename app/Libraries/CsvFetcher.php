<?php

namespace App\Libraries;

use Config\Services;

class CsvFetcher
{
    public function get(string $url, bool $forceRefresh = false): array
    {
        $key   = 'csv_' . md5($url);
        $cache = cache();

        if (!$forceRefresh && ($cached = $cache->get($key))) {
            return $cached;
        }

        // Coba beberapa kandidat URL (direct + proxy "reader")
        $base = preg_replace('#^https?://#', '', $url);
        $candidates = [
            $url,                                                // direct
            'https://r.jina.ai/https://' . $base,               // proxy 1 (https)
            'https://r.jina.ai/http://'  . $base,               // proxy 1 (http)
        ];

        $client = Services::curlrequest([
            'timeout'         => 20,
            'http_errors'     => false,
            'verify'          => false,
            'allow_redirects' => true,
            'headers'         => [
                'User-Agent' => 'Mozilla/5.0 (CsvFetcher CI4)',
                'Accept'     => 'text/csv, text/plain, */*',
            ],
        ]);

        $final = ['columns' => [], 'rows' => []];

        foreach ($candidates as $tryUrl) {
            log_message('debug', 'CsvFetcher mencoba URL: ' . $tryUrl);

            try {
                $resp = $client->get($tryUrl);
            } catch (\Throwable $e) {
                log_message('error', 'Curl error: ' . $e->getMessage());
                continue;
            }

            $code = $resp->getStatusCode();
            log_message('debug', "CsvFetcher HTTP $code untuk $tryUrl");

            if ($code !== 200) {
                continue;
            }

            $body = $resp->getBody();

            // deteksi HTML
            if (!$body) {
                log_message('debug', "CsvFetcher: body kosong untuk $tryUrl");
                continue;
            }

            // deteksi halaman login (Google) yang lolos dari cek HTML
            if (preg_match('/Sign in - Google Accounts|accounts\.google\.com/i', substr($body, 0, 500))) {
                log_message('debug', "CsvFetcher: terdeteksi halaman login Google, skip: $tryUrl");
                continue;
            }

            if (preg_match('/<\s*html/i', substr($body, 0, 400))) {
                log_message('debug', "CsvFetcher: body tampak HTML (bukan CSV), skip: $tryUrl");
                // optional: log cuplikan awal untuk analisis
                log_message('debug', 'Body preview: ' . substr($body, 0, 200));
                continue;
            }

            [$columns, $rows] = $this->parseCsv($body);
            log_message('debug', 'CsvFetcher parse: kolom=' . json_encode($columns) . ' | baris=' . count($rows));

            if (!empty($columns) && !empty($rows)) {
                $final = ['columns' => $columns, 'rows' => $rows];
                break;
            }
        }

        // HANYA cache jika ada data
        if (!empty($final['columns']) && !empty($final['rows'])) {
            $cache->save($key, $final, 600);
        }

        return $final;
    }

    /** @return array{0:array,1:array} */
    private function parseCsv(string $csv): array
    {
        // Pecah baris & buang kosong
        $lines = preg_split("/\r\n|\n|\r/", $csv);
        if (!$lines) return [[], []];

        // Helper: bersihkan BOM, NBSP, zero-width, trim
        $clean = function (string $s): string {
            // BOM UTF-8, ZWSP, NBSP
            $s = preg_replace('/^\xEF\xBB\xBF/u', '', $s);     // BOM
            $s = preg_replace('/[\x{200B}\x{200C}\x{200D}\x{00A0}]/u', '', $s); // ZWSP/NBSP
            return trim($s ?? '');
        };

        // Parse setiap baris pakai str_getcsv (asumsikan delimiter koma dari Google Sheets)
        $rows = [];
        foreach ($lines as $line) {
            if ($line === '' || $line === null) continue;
            $cells = array_map($clean, str_getcsv($line, ',')); // delimiter koma
            // Buang baris yang betul-betul kosong
            if (count(array_filter($cells, fn($x) => $x !== '')) === 0) continue;
            $rows[] = $cells;
        }
        if (count($rows) < 2) return [[], []];

        // Deteksi baris header: cari baris yang punya >= 2 kolom "tahun" (4 digit) atau mengandung "Tahun"
        $headerIdx = 0;
        $isYear = fn($x) => preg_match('/^\d{4}$/', $x);
        for ($r = 0; $r < min(count($rows), 12); $r++) {
            $yearCount = 0;
            foreach ($rows[$r] as $c) if ($isYear($c)) $yearCount++;
            if ($yearCount >= 2 || in_array('Tahun', $rows[$r], true)) {
                $headerIdx = $r;
                break;
            }
        }

        $columns = $rows[$headerIdx];
        $body = array_slice($rows, $headerIdx + 1);

        // Normalisasi angka: hapus ribuan, set desimal ke titik
        $toNumber = function ($v) {
            if ($v === '' || $v === null) return null;
            if (is_numeric($v)) return $v + 0;

            $s = $v;

            // 1) 12.345,67 (ID/EU) -> remove ., ganti , -> .
            if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $s)) {
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);
                return is_numeric($s) ? ($s + 0) : null;
            }
            // 2) 12,345.67 (US) -> remove , (ribuan)
            if (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $s)) {
                $s = str_replace(',', '', $s);
                return is_numeric($s) ? ($s + 0) : null;
            }
            // 3) 12345,67 (ID) -> , sebagai desimal
            if (preg_match('/^\d+,\d+$/', $s)) {
                $s = str_replace(',', '.', $s);
                return is_numeric($s) ? ($s + 0) : null;
            }
            // 4) 12345.67 atau 12345
            if (preg_match('/^\d+(\.\d+)?$/', $s)) {
                return $s + 0;
            }
            return null;
        };

        // Bangun array of associative rows
        $outRows = [];
        foreach ($body as $r) {
            $row = [];
            foreach ($columns as $i => $name) {
                $name = $clean((string)($name ?? ''));
                $val  = $r[$i] ?? null;

                // Coba cast angka
                if (is_string($val)) {
                    $n = $toNumber($val);
                    $row[$name] = $n !== null ? $n : $clean($val);
                } else {
                    $row[$name] = $val;
                }
            }
            $outRows[] = $row;
        }
        // Bersihkan nama kolom sekali lagi (jaga-jaga)
        $columns = array_map($clean, $columns);

        return [$columns, $outRows];
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> ac3bfa8de96bd057f22d001c5e926d0f1b4e1485
