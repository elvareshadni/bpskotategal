<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\CsvFetcher;

class Indicators extends BaseController
{
    public function index()
    {
        $key = strtoupper($this->request->getGet('key') ?? '');
        if (!$key) return $this->fail('Missing ?key', 400);

        // Samakan dengan peta di Dashboard::index (atau pindahkan ke Config jika mau)
        /*$map = [
            'LUAS_KEPENDUDUKAN' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=1557804739&single=true&output=csv',
            'ANGKA_KEMISKINAN'  => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=901949954&single=true&output=csv',
            'INFLASI_UMUM'      => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=882681484&single=true&output=csv',
            'IPM'               => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=896003102&single=true&output=csv',
            'PDRB'              => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=1018033332&single=true&output=csv',
            'KETENAGAKERJAAN'   => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=1275628956&single=true&output=csv',
            'KESEJAHTERAAN'     => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=2060752569&single=true&output=csv',
        ];*/

        $exec = getenv('CSV_URL'); // dari .env
        log_message('debug', 'CSV EXEC URL = ' . ($exec ?: '(kosong)'));
        log_message('debug', 'Indicator key = ' . $key);

        $map = [
            'LUAS_KEPENDUDUKAN' => $exec . '?sheet=LUAS_KEPENDUDUKAN',
            'ANGKA_KEMISKINAN'  => $exec . '?sheet=ANGKA_KEMISKINAN',
            'INFLASI_UMUM'      => $exec . '?sheet=INFLASI_UMUM',
            'IPM'               => $exec . '?sheet=IPM',
            'PDRB'              => $exec . '?sheet=PDRB',
            'KETENAGAKERJAAN'   => $exec . '?sheet=KETENAGAKERJAAN',
            'KESEJAHTERAAN'     => $exec . '?sheet=KESEJAHTERAAN',
        ];

        if (!isset($map[$key])) return $this->fail('Unknown key', 404);
        $url = $map[$key];

        $force = $this->request->getGet('nocache') === '1';
        log_message('debug', "Fetch URL untuk {$key} = {$url} | nocache=" . ($force ? '1' : '0'));
        $service = new CsvFetcher();
        $json = $service->get($url, $force);

        // BANTUAN DEBUG: kalau kosong, kasih error jelas
        if (empty($json['columns']) || empty($json['rows'])) {
            log_message('error', "CSV kosong atau gagal parse untuk {$key} dari {$url}");
            return $this->fail('CSV kosong/gagal diambil. Cek log.', 502);
        }

        return $this->response->setJSON([
            'ok'      => true,
            'source'  => $key,
            'columns' => $json['columns'], // array of header
            'rows'    => $json['rows'],    // array of associative row: ["Tahun"=>..., "X"=>...]
        ]);
    }

    private function fail($msg, $status = 400)
    {
        return $this->response->setStatusCode($status)->setJSON(['ok' => false, 'error' => $msg]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> ac3bfa8de96bd057f22d001c5e926d0f1b4e1485
