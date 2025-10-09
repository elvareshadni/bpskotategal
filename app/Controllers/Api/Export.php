<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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

    private function addr(int $colIndex, int $row): string
    {
        return Coordinate::stringFromColumnIndex($colIndex) . $row;
    }

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

    // ---------- Styling Helpers ----------
    private function applyTitleBlock($sheet, string $title, string $indikator, string $region, string $timeline, string $unit): int
    {
        // A1: Title
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // A2..B3: Meta
        $sheet->setCellValue('A2', 'Indikator');
        $sheet->setCellValue('B2', $indikator);
        $sheet->setCellValue('A3', 'Region');
        $sheet->setCellValue('B3', $region);
        $sheet->setCellValue('D2', 'Timeline');
        $sheet->setCellValue('E2', $timeline);
        $sheet->setCellValue('D3', 'Satuan');
        $sheet->setCellValue('E3', $unit ?: '-');

        $sheet->getStyle('A2:A3')->getFont()->setBold(true);
        $sheet->getStyle('D2:D3')->getFont()->setBold(true);

        return 5; // header tabel mulai baris 5
    }

    private function styleTable($sheet, int $startRow, int $startCol, int $endRow, int $endCol): void
    {
        // Header
        $hdrRange = $this->addr($startCol, $startRow) . ':' . $this->addr($endCol, $startRow);
        $sheet->getStyle($hdrRange)->getFont()->setBold(true);
        $sheet->getStyle($hdrRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($hdrRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E7F1FF');

        // Border all
        $tblRange = $this->addr($startCol, $startRow) . ':' . $this->addr($endCol, $endRow);
        $sheet->getStyle($tblRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('B7C5D3');

        // Period column center
        $sheet->getStyle($this->addr($startCol, $startRow + 1) . ':' . $this->addr($startCol, $endRow))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Autofilter
        $sheet->setAutoFilter($hdrRange);
    }

    /**
     * Build workbook berisi seluruh subindikator (dipakai oleh export & template).
     */
    private function buildWorkbook(int $indicatorId, int $regionId): ?Spreadsheet
    {
        $db = \Config\Database::connect();

        $indicator = $db->table('indicators')->where('id', $indicatorId)->get()->getRowArray();
        if (!$indicator) return null;
        $region     = $db->table('regions')->where('id', $regionId)->get()->getRowArray();
        $regionName = $region['name'] ?? 'Wilayah';

        $rows = $db->table('indicator_rows')
            ->where('indicator_id', $indicatorId)
            ->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')
            ->get()->getResultArray();
        if (!$rows) return null;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($rows as $r) {
            $rowId    = (int)$r['id'];
            $timeline = strtolower($r['timeline']);
            $dtype    = strtolower($r['data_type']);
            $unit     = $r['unit'] ?? '';
            $title    = $r['subindikator'];

            // Vars
            $vars = $db->table('indicator_row_vars')
                ->where('row_id', $rowId)
                ->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')
                ->get()->getResultArray();
            $varMap = [];
            foreach ($vars as $v) $varMap[(int)$v['id']] = $v['name'];

            // Values
            $values = $db->table('indicator_values')
                ->select('year,quarter,month,var_id,value')
                ->where(['row_id' => $rowId, 'region_id' => $regionId])
                ->orderBy('year', 'ASC')->orderBy('quarter', 'ASC')->orderBy('month', 'ASC')
                ->get()->getResultArray();

            // Period list
            $periods = [];
            foreach ($values as $val) {
                $y = $val['year'];
                $q = $val['quarter'];
                $m = $val['month'];
                $key = $this->periodKey($timeline, $y, $q, $m);
                if (!isset($periods[$key])) {
                    $label = $key;
                    if ($timeline === 'quarterly') $label = sprintf('%04d-Q%d', (int)$y, (int)$q);
                    if ($timeline === 'monthly')   $label = sprintf('%04d-%02d', (int)$y, (int)$m);
                    $periods[$key] = ['label' => $label, 'year' => $y, 'quarter' => $q, 'month' => $m];
                }
            }
            usort($periods, fn($a, $b) => $this->periodSortTuple($timeline, $a) <=> $this->periodSortTuple($timeline, $b));

            if (empty($periods)) {
                $now = (int)date('Y');
                if ($timeline === 'yearly') {
                    for ($y = $now - 4; $y <= $now; $y++) {
                        $periods[] = ['label' => (string)$y, 'year' => $y, 'quarter' => null, 'month' => null];
                    }
                } elseif ($timeline === 'quarterly') {
                    $y = $now; // atau pilih tahun terakhir yang kamu mau
                    for ($q = 1; $q <= 4; $q++) {
                        $periods[] = ['label' => sprintf('%04d-Q%d', $y, $q), 'year' => $y, 'quarter' => $q, 'month' => null];
                    }
                } else { // monthly
                    $y = $now;
                    for ($m = 1; $m <= 12; $m++) {
                        $periods[] = ['label' => sprintf('%04d-%02d', $y, $m), 'year' => $y, 'quarter' => null, 'month' => $m];
                    }
                }
            }

            // Pivot
            $pivot = [];
            foreach ($values as $val) {
                $key = $this->periodKey($timeline, $val['year'], $val['quarter'], $val['month']);
                $vid = $val['var_id'] ? (int)$val['var_id'] : 0;
                $pivot[$key][$vid] = $val['value'];
            }

            // Sheet
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($this->sheetTitle($title));
            $startRow = $this->applyTitleBlock($sheet, $title, $indicator['name'], $regionName, $this->timelineLabel($timeline), $unit);

            // Header
            $col = 1;
            $sheet->setCellValue($this->addr($col++, $startRow), 'Periode');
            $varOrder = [];
            if ($dtype === 'timeseries') {
                $sheet->setCellValue($this->addr($col++, $startRow), 'Nilai' . ($unit ? " ({$unit})" : ''));
            } else {
                $varOrder = array_keys($varMap);
                if (!$varOrder) {
                    // fallback: ambil dari data bila belum ada var
                    $tmp = [];
                    foreach ($values as $val) if (!empty($val['var_id'])) $tmp[(int)$val['var_id']] = true;
                    $varOrder = array_keys($tmp);
                    sort($varOrder);
                }
                foreach ($varOrder as $vid) {
                    $sheet->setCellValue($this->addr($col++, $startRow), $varMap[$vid] ?? ("Var " . $vid));
                }
            }

            // Data rows (isi data yang sudah ada)
            $rIdx = $startRow + 1;
            foreach ($periods as $p) {
                $key = $this->periodKey($timeline, $p['year'], $p['quarter'], $p['month']);
                $col = 1;
                $sheet->setCellValue($this->addr($col++, $rIdx), $p['label']);
                if ($dtype === 'timeseries') {
                    $sheet->setCellValue($this->addr($col++, $rIdx), $pivot[$key][0] ?? null);
                } else {
                    foreach ($varOrder as $vid) {
                        $sheet->setCellValue($this->addr($col++, $rIdx), $pivot[$key][$vid] ?? null);
                    }
                }
                $rIdx++;
            }

            $endRow = max($startRow + 1, $rIdx - 1);
            $endCol = max(2, $col - 1);
            $this->styleTable($sheet, $startRow, 1, $endRow, $endCol);
            $this->autosizeCols($sheet, $endCol);
        }

        $spreadsheet->setActiveSheetIndex(0);
        return $spreadsheet;
    }

    // ---------- EXPORT (identik seperti semula) ----------
    public function indicatorXlsx()
    {
        $indicatorId = (int)$this->request->getGet('indicator_id');
        $regionId    = (int)$this->request->getGet('region_id');
        if (!$indicatorId || !$regionId) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'msg' => 'indicator_id dan region_id wajib diisi']);
        }

        $wb = $this->buildWorkbook($indicatorId, $regionId);
        if (!$wb) return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'msg' => 'Data tidak ditemukan']);

        // Nama file
        $db = \Config\Database::connect();
        $indicator = $db->table('indicators')->where('id', $indicatorId)->get()->getRowArray();
        $region    = $db->table('regions')->where('id', $regionId)->get()->getRowArray();
        $file = ($indicator['name'] ?? 'Indikator') . ' (' . ($region['name'] ?? 'Wilayah') . ').xlsx';

        $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $file . '"');
        $this->response->setHeader('Cache-Control', 'max-age=0');
        (new Xlsx($wb))->save('php://output');
        return;
    }

    // ---------- TEMPLATE IMPORT (sekarang tampilannya sama persis dengan Export & berisi data) ----------
    public function indicatorTemplateXlsx()
    {
        $indicatorId = (int)$this->request->getGet('indicator_id');
        $regionId    = (int)$this->request->getGet('region_id');
        if (!$indicatorId || !$regionId) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'msg' => 'indicator_id dan region_id wajib diisi']);
        }

        $wb = $this->buildWorkbook($indicatorId, $regionId);
        if (!$wb) return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'msg' => 'Data tidak ditemukan']);

        // Nama file beri label Template biar tidak tertukar (tampilan sheet tetap sama)
        $db = \Config\Database::connect();
        $indicator = $db->table('indicators')->where('id', $indicatorId)->get()->getRowArray();
        $region    = $db->table('regions')->where('id', $regionId)->get()->getRowArray();
        $file = 'TEMPLATE Import - ' . ($indicator['name'] ?? 'Indikator') . ' (' . ($region['name'] ?? 'Wilayah') . ').xlsx';

        $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $file . '"');
        $this->response->setHeader('Cache-Control', 'max-age=0');
        (new Xlsx($wb))->save('php://output');
        return;
    }
}
