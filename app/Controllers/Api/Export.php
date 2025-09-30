<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Export extends BaseController
{
    private function sheetTitle(string $s): string
    {
        $s = preg_replace('/[\\\\\\/\\?\\*\\[\\]:]/', ' ', $s);
        $s = trim($s);
        return mb_substr($s, 0, 31);
    }

    private function timelineLabel(string $t): string
    {
        $t = strtolower($t);
        return [
            'yearly'    => 'TAHUNAN',
            'quarterly' => 'TRIWULAN',
            'monthly'   => 'BULANAN',
        ][$t] ?? strtoupper($t);
    }

    /** Ubah index kolom (1-based) + baris -> alamat sel, mis. (2,5) => "B5" */
    private function addr(int $colIndex, int $row): string
    {
        return Coordinate::stringFromColumnIndex($colIndex) . $row;
    }

    /** Autosize pakai nama kolom (A, B, C, ...) agar kompatibel di semua versi */
    private function autosizeCols($sheet, int $maxCol): void
    {
        for ($c = 1; $c <= $maxCol; $c++) {
            $col = Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function periodKey(string $timeline, ?int $year, ?int $q, ?int $m): string
    {
        if ($timeline === 'yearly')    return sprintf('%04d', (int)$year);
        if ($timeline === 'quarterly') return sprintf('%04d-Q%d', (int)$year, (int)$q);
        return sprintf('%04d-%02d', (int)$year, (int)$m);
    }

    private function periodSortTuple(string $timeline, array $r): array
    {
        $y = (int)($r['year'] ?? 0);
        if ($timeline === 'yearly')    return [$y, 0, 0];
        if ($timeline === 'quarterly') return [$y, (int)($r['quarter'] ?? 0), 0];
        return [$y, 0, (int)($r['month'] ?? 0)];
    }

    public function indicatorXlsx()
    {
        $indicatorId = (int) $this->request->getGet('indicator_id');
        $regionId    = (int) $this->request->getGet('region_id');

        if (!$indicatorId || !$regionId) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false, 'msg' => 'indicator_id dan region_id wajib diisi'
            ]);
        }

        $db = \Config\Database::connect();

        $indicator = $db->table('indicators')->where('id', $indicatorId)->get()->getRowArray();
        if (!$indicator) {
            return $this->response->setStatusCode(404)->setJSON(['ok'=>false,'msg'=>'Indikator tidak ditemukan']);
        }

        $region     = $db->table('regions')->where('id', $regionId)->get()->getRowArray();
        $regionName = $region['name'] ?? 'Wilayah';

        $rows = $db->table('indicator_rows')
            ->where('indicator_id', $indicatorId)
            ->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')
            ->get()->getResultArray();

        if (!$rows) {
            return $this->response->setStatusCode(404)->setJSON(['ok'=>false,'msg'=>'Belum ada subindikator']);
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($rows as $r) {
            $rowId     = (int)$r['id'];
            $timeline  = strtolower($r['timeline']);        // yearly|quarterly|monthly
            $dataType  = strtolower($r['data_type']);       // timeseries|jumlah_kategori|proporsi
            $unit      = $r['unit'] ?? '';
            $title     = $r['subindikator'];

            $vars = $db->table('indicator_row_vars')
                ->where('row_id', $rowId)
                ->orderBy('sort_order','ASC')->orderBy('id','ASC')
                ->get()->getResultArray();
            $varMap = [];
            foreach ($vars as $v) $varMap[(int)$v['id']] = $v['name'];

            $values = $db->table('indicator_values')
                ->select('year, quarter, month, var_id, value')
                ->where(['row_id'=>$rowId, 'region_id'=>$regionId])
                ->orderBy('year','ASC')->orderBy('quarter','ASC')->orderBy('month','ASC')
                ->get()->getResultArray();

            $periods = [];
            foreach ($values as $val) {
                $y = $val['year']; $q = $val['quarter']; $m = $val['month'];
                $key = $this->periodKey($timeline, $y, $q, $m);
                if (!isset($periods[$key])) {
                    $label = $key;
                    if ($timeline === 'quarterly') $label = sprintf('%04d-TW%d', (int)$y, (int)$q);
                    if ($timeline === 'monthly')   $label = sprintf('%04d-%02d', (int)$y, (int)$m);
                    $periods[$key] = ['label'=>$label,'year'=>$y,'quarter'=>$q,'month'=>$m];
                }
            }
            usort($periods, function($a, $b) use ($timeline){
                $ta = $this->periodSortTuple($timeline, $a);
                $tb = $this->periodSortTuple($timeline, $b);
                return $ta <=> $tb;
            });

            $pivot = [];
            foreach ($values as $val) {
                $key = $this->periodKey($timeline, $val['year'], $val['quarter'], $val['month']);
                $vid = $val['var_id'] ? (int)$val['var_id'] : 0;
                $pivot[$key][$vid] = $val['value'];
            }

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($this->sheetTitle($title));

            // Header meta
            $sheet->setCellValue('A1', $title);
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);

            $sheet->setCellValue('A2', 'Satuan');   $sheet->setCellValue('B2', $unit ?: '-');
            $sheet->setCellValue('A3', 'Timeline'); $sheet->setCellValue('B3', $this->timelineLabel($timeline));

            $startRow = 5;

            if ($dataType === 'timeseries') {
                // Header
                $sheet->setCellValue($this->addr(1, $startRow), 'Periode');
                $sheet->setCellValue($this->addr(2, $startRow), 'Nilai' . ($unit ? " ({$unit})" : ''));
                $sheet->getStyle("A{$startRow}:B{$startRow}")->getFont()->setBold(true);

                // Data
                $rIdx = $startRow + 1;
                foreach ($periods as $p) {
                    $key = $this->periodKey($timeline, $p['year'], $p['quarter'], $p['month']);
                    $sheet->setCellValue($this->addr(1, $rIdx), $p['label']);
                    $sheet->setCellValue($this->addr(2, $rIdx), $pivot[$key][0] ?? null);
                    $rIdx++;
                }
                $this->autosizeCols($sheet, 2);
            } else {
                // Multi variabel: jumlah_kategori / proporsi
                $sheet->setCellValue($this->addr(1, $startRow), 'Periode');

                $varOrder = array_keys($varMap);
                if (!$varOrder) {
                    $tmp = [];
                    foreach ($values as $val) {
                        if (!empty($val['var_id'])) $tmp[(int)$val['var_id']] = true;
                    }
                    $varOrder = array_keys($tmp);
                    sort($varOrder);
                }

                // Header variabel
                $col = 2;
                foreach ($varOrder as $vid) {
                    $name = $varMap[$vid] ?? ("Var ".$vid);
                    $sheet->setCellValue($this->addr($col++, $startRow), $name);
                }
                $sheet->getStyle("A{$startRow}:{$sheet->getHighestColumn()}{$startRow}")
                      ->getFont()->setBold(true);

                // Data per periode
                $rIdx = $startRow + 1;
                foreach ($periods as $p) {
                    $key = $this->periodKey($timeline, $p['year'], $p['quarter'], $p['month']);
                    $sheet->setCellValue($this->addr(1, $rIdx), $p['label']);
                    $col = 2;
                    foreach ($varOrder as $vid) {
                        $sheet->setCellValue($this->addr($col++, $rIdx), $pivot[$key][$vid] ?? null);
                    }
                    $rIdx++;
                }
                $this->autosizeCols($sheet, 1 + max(1, count($varOrder)));
            }

            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $file = ($indicator['name'] ?? 'Indikator') . ' (' . $regionName . ').xlsx';

        $this->response->setHeader(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $this->response->setHeader('Content-Disposition','attachment; filename="'.$file.'"');
        $this->response->setHeader('Cache-Control','max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        return;
    }
}
