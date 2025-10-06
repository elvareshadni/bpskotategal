<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IndicatorModel;
use App\Models\IndicatorRowModel;
use App\Models\IndicatorRowVarModel;
use App\Models\IndicatorValueModel;
use App\Models\RegionModel;

class Indicators extends BaseController
{
    public function index()
    {
        return $this->response->setJSON(['ok' => true, 'note' => 'Use /api/* endpoints']);
    }

    // ========== DASHBOARD APIS ==========
    public function apiRegions()
    {
        $rows = (new RegionModel())->orderBy('is_default', 'DESC')->orderBy('name', 'ASC')->findAll();
        $out = array_map(fn($r) => [
            'id' => (int)$r['id'],
            'name' => $r['name'],
            'code_bps' => $r['code_bps'],
            'is_default' => (int)$r['is_default']
        ], $rows);
        return $this->response->setJSON(['ok' => true, 'regions' => $out]);
    }

    public function apiIndicators()
    {
        $regionId = (int)($this->request->getGet('region_id') ?? 0);
        $m = new IndicatorModel();
        if ($regionId > 0) $m->where('region_id', $regionId);
        $rows = $m->orderBy('name', 'ASC')->findAll();
        $out = array_map(fn($r) => [
            'id' => (int)$r['id'],
            'name' => $r['name'],
            'code' => $r['code'],
            'region_id' => (int)$r['region_id'],
        ], $rows);
        return $this->response->setJSON(['ok' => true, 'indicators' => $out]);
    }

    public function apiRows()
    {
        $indicatorId = (int)($this->request->getGet('indicator_id') ?? 0);
        if ($indicatorId <= 0) return $this->response->setJSON(['ok' => false, 'error' => 'indicator_id required']);
        $rows = (new IndicatorRowModel())->where('indicator_id', $indicatorId)->orderBy('sort_order', 'ASC')->findAll();

        $out = array_map(function ($r) {
            return [
                'id' => (int)$r['id'],
                'subindikator' => $r['subindikator'],
                'timeline' => strtoupper($r['timeline']),
                'data_type' => strtoupper($r['data_type']),
                'unit' => $r['unit'],
                'interpretasi' => $r['interpretasi'],
            ];
        }, $rows);

        return $this->response->setJSON(['ok' => true, 'rows' => $out]);
    }

    // SINGLE / MULTI series
    public function apiSeries()
    {
        $rowId    = (int)($this->request->getGet('row_id') ?? 0);
        $regionId = (int)($this->request->getGet('region_id') ?? 0);
        $window   = (string)($this->request->getGet('window') ?? 'all');
        $year     = (int)($this->request->getGet('year') ?? 0);
        $quarter  = (int)($this->request->getGet('quarter') ?? 0);
        $month    = (int)($this->request->getGet('month') ?? 0);
        $pickVar  = $this->request->getGet('var_id');
        $multi    = (int)($this->request->getGet('multi') ?? 0) === 1; // ADDED

        if ($rowId <= 0 || $regionId <= 0) {
            return $this->response->setJSON(['ok' => false, 'error' => 'row_id & region_id required']);
        }

        $row = (new IndicatorRowModel())->find($rowId);
        if (!$row) {
            return $this->response->setJSON(['ok' => false, 'error' => 'row not found']);
        }

        $timeline = $row['timeline'];     // yearly|quarterly|monthly
        $dtype    = $row['data_type'];    // timeseries|jumlah_kategori

        // ======================= MULTI DATASET (JUMLAH_KATEGORI) ======================= // ADDED
        if ($dtype === 'jumlah_kategori' && $multi) {
            $varM = new IndicatorRowVarModel();
            $vars = $varM->where('row_id', $rowId)->orderBy('sort_order', 'ASC')->findAll();

            if (empty($vars)) {
                return $this->response->setJSON([
                    'ok' => true,
                    'labels' => [],
                    'datasets' => [],
                    'meta' => [
                        'timeline' => strtoupper($timeline),
                        'unit' => $row['unit'],
                        'desc' => $row['subindikator'],
                        'interpretasi' => $row['interpretasi'],
                        'data_type' => strtoupper($dtype),
                        'vars' => []
                    ]
                ]);
            }

            // Tentukan label periode yang dipakai
            $labels  = [];
            $periods = []; // [['year'=>..,'quarter'=>..,'month'=>..], ...]

            if ($timeline === 'yearly') {
                // Gabungan semua tahun yang ada
                $valM = new IndicatorValueModel();
                $allYears = $valM->select('year')
                    ->where('row_id', $rowId)->where('region_id', $regionId)
                    ->where('year IS NOT NULL', null, false)
                    ->groupBy('year')->orderBy('year', 'ASC')->findAll();
                $years = array_values(array_unique(array_map(fn($r) => (int)$r['year'], $allYears)));

                if ($window === 'last3' || $window === 'last5') {
                    $take = $window === 'last3' ? 3 : 5;
                    $years = array_slice($years, max(0, count($years) - $take));
                } elseif ($year > 0) {
                    $years = [$year];
                }

                foreach ($years as $y) {
                    $labels[] = (string)$y;
                    $periods[] = ['year' => $y, 'quarter' => null, 'month' => null];
                }
            } elseif ($timeline === 'quarterly') {
                $y = $year ?: (int)date('Y');
                foreach ([1, 2, 3, 4] as $q) {
                    $labels[] = $y . ' Q' . $q;
                    $periods[] = ['year' => $y, 'quarter' => $q, 'month' => null];
                }
            } else { // monthly
                $y = $year ?: (int)date('Y');
                for ($m = 1; $m <= 12; $m++) {
                    $labels[] = sprintf('%d-%02d', $y, $m);
                    $periods[] = ['year' => $y, 'quarter' => null, 'month' => $m];
                }
            }

            // Ambil data per variabel
            $datasets = [];
            foreach ($vars as $v) {
                $valM = new IndicatorValueModel(); // NEW per loop agar where tidak menumpuk
                $q = $valM->where('row_id', $rowId)->where('region_id', $regionId)->where('var_id', (int)$v['id']);

                if ($timeline === 'yearly') {
                    $yrs = array_map(fn($p) => $p['year'], $periods);
                    if (!empty($yrs)) $q->whereIn('year', $yrs);
                    $q->orderBy('year', 'ASC');
                } elseif ($timeline === 'quarterly') {
                    $q->where('year', $periods[0]['year'])->orderBy('quarter', 'ASC');
                } else {
                    $q->where('year', $periods[0]['year'])->orderBy('month', 'ASC');
                }

                $vals = $q->findAll();
                $map = [];
                foreach ($vals as $vv) {
                    $ky = $vv['year'] . '|' . (int)$vv['quarter'] . '|' . (int)$vv['month'];
                    $map[$ky] = is_null($vv['value']) ? null : (float)$vv['value'];
                }

                $data = [];
                foreach ($periods as $p) {
                    $ky = $p['year'] . '|' . (int)$p['quarter'] . '|' . (int)$p['month'];
                    $data[] = $map[$ky] ?? null;
                }

                $datasets[] = [
                    'var_id' => (int)$v['id'],
                    'name'   => $v['name'],
                    'values' => $data,
                ];
            }

            return $this->response->setJSON([
                'ok' => true,
                'labels' => $labels,
                'datasets' => $datasets,
                'meta' => [
                    'timeline' => strtoupper($timeline),
                    'unit' => $row['unit'],
                    'desc' => $row['subindikator'],
                    'interpretasi' => $row['interpretasi'],
                    'data_type' => strtoupper($dtype),
                    'vars' => array_map(fn($v) => ['id' => (int)$v['id'], 'name' => $v['name']], $vars),
                ]
            ]);
        }
        // ===================== END MULTI DATASET =====================

        // ====== MODE LAMA: single series (timeseries atau jumlah_kategori dgn 1 var) ======
        $valM     = new IndicatorValueModel();
        $usedVarId = null;
        $varsMeta  = [];

        if ($dtype === 'timeseries') {
            $usedVarId = null;
        } else {
            $varM = new IndicatorRowVarModel();
            $vars = $varM->where('row_id', $rowId)->orderBy('sort_order', 'ASC')->findAll();
            $varsMeta = array_map(fn($v) => ['id' => (int)$v['id'], 'name' => $v['name']], $vars);

            if (empty($vars)) {
                return $this->response->setJSON([
                    'ok' => true,
                    'labels' => [],
                    'values' => [],
                    'meta' => [
                        'timeline' => strtoupper($timeline),
                        'unit' => $row['unit'],
                        'desc' => $row['subindikator'],
                        'interpretasi' => $row['interpretasi'],
                        'data_type' => strtoupper($dtype),
                        'var_id' => null,
                        'vars' => [],
                        'note' => 'No variables defined for jumlah_kategori'
                    ]
                ]);
            }

            if ($pickVar !== null && $pickVar !== '')      $usedVarId = (int)$pickVar;
            elseif (count($vars) === 1)                    $usedVarId = (int)$vars[0]['id'];
            else                                           $usedVarId = (int)$vars[0]['id']; // fallback
        }

        $builder = $valM->where('row_id', $rowId)->where('region_id', $regionId);
        if (is_null($usedVarId)) $builder->where('var_id', null);
        else                     $builder->where('var_id', $usedVarId);

        if ($timeline === 'yearly') {
            if ($window === 'last3' || $window === 'last5') {
                $allYearsQ = $valM->select('year')->where('row_id', $rowId)->where('region_id', $regionId);
                if (is_null($usedVarId)) $allYearsQ->where('var_id', null);
                else                     $allYearsQ->where('var_id', $usedVarId);

                $all = $allYearsQ->orderBy('year', 'ASC')->findAll();
                $years = array_values(array_unique(array_map(fn($r) => (int)$r['year'], $all)));
                $take  = ($window === 'last3') ? 3 : 5;
                $years = array_slice($years, max(0, count($years) - $take));
                if ($years) $builder->whereIn('year', $years);
            } elseif ($year > 0) {
                $builder->where('year', $year);
            }
            $builder->orderBy('year', 'ASC');
        } elseif ($timeline === 'quarterly') {
            $y = $year ?: (int)date('Y');
            $builder->where('year', $y)->orderBy('quarter', 'ASC');
        } else {
            $y = $year ?: (int)date('Y');
            $builder->where('year', $y)->orderBy('month', 'ASC');
        }

        $vals = $builder->findAll();

        $labels = [];
        $values = [];
        if ($timeline === 'yearly') {
            $map = [];
            foreach ($vals as $v) $map[(int)$v['year']] = is_null($v['value']) ? null : (float)$v['value'];
            ksort($map);
            foreach ($map as $y => $val) {
                $labels[] = (string)$y;
                $values[] = $val;
            }
        } elseif ($timeline === 'quarterly') {
            foreach ($vals as $v) {
                $labels[] = $v['year'] . ' Q' . $v['quarter'];
                $values[] = is_null($v['value']) ? null : (float)$v['value'];
            }
        } else {
            foreach ($vals as $v) {
                $labels[] = sprintf('%d-%02d', $v['year'], $v['month']);
                $values[] = is_null($v['value']) ? null : (float)$v['value'];
            }
        }

        return $this->response->setJSON([
            'ok'     => true,
            'labels' => $labels,
            'values' => $values,
            'meta'   => [
                'timeline'     => strtoupper($timeline),
                'unit'         => $row['unit'],
                'desc'         => $row['subindikator'],
                'interpretasi' => $row['interpretasi'],
                'data_type'    => strtoupper($dtype),
                'var_id'       => $usedVarId,  // null untuk timeseries
                'vars'         => $varsMeta,
            ]
        ]);
    }

    // PROPORSI
    public function apiProportion()
    {
        $rowId    = (int)($this->request->getGet('row_id') ?? 0);
        $regionId = (int)($this->request->getGet('region_id') ?? 0);
        $year     = (int)($this->request->getGet('year') ?? 0);
        $quarter  = (int)($this->request->getGet('quarter') ?? 0);
        $month    = (int)($this->request->getGet('month') ?? 0);

        if ($rowId <= 0 || $regionId <= 0 || $year <= 0) return $this->response->setJSON(['ok' => false, 'error' => 'row_id, region_id, year required']);

        $row = (new IndicatorRowModel())->find($rowId);
        if (!$row) return $this->response->setJSON(['ok' => false, 'error' => 'row not found']);
        if ($row['data_type'] !== 'proporsi') return $this->response->setJSON(['ok' => false, 'error' => 'not a proportion row']);

        $vars = (new IndicatorRowVarModel())->where('row_id', $rowId)->orderBy('sort_order', 'ASC')->findAll();
        $labels = array_map(fn($v) => $v['name'], $vars);
        $varIds = array_map(fn($v) => (int)$v['id'], $vars);

        $valM = new IndicatorValueModel();
        $valM->where('row_id', $rowId)->where('region_id', $regionId)->where('year', $year);
        if ($row['timeline'] === 'quarterly') $valM->where('quarter', $quarter ?: 1);
        if ($row['timeline'] === 'monthly')   $valM->where('month', $month ?: 1);
        $vals = $valM->whereIn('var_id', $varIds)->findAll();

        $map = [];
        foreach ($vals as $v) $map[(int)$v['var_id']] = is_null($v['value']) ? null : (float)$v['value'];

        $data = [];
        foreach ($varIds as $vid) $data[] = $map[$vid] ?? null;

        return $this->response->setJSON([
            'ok' => true,
            'labels' => $labels,
            'values' => $data,
            'meta' => [
                'timeline' => strtoupper($row['timeline']),
                'unit' => $row['unit'],
                'desc' => $row['subindikator'],
                'interpretasi' => $row['interpretasi'],
                'year' => $year,
                'quarter' => $quarter,
                'month' => $month
            ]
        ]);
    }

    public function periods()
    {
        $rowId    = (int) ($this->request->getGet('row_id') ?? 0);
        $regionId = (int) ($this->request->getGet('region_id') ?? 0);

        if (!$rowId || !$regionId) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'row_id & region_id wajib']);
        }

        $row = (new IndicatorRowModel())
            ->select('timeline')
            ->where('id', $rowId)
            ->first();
        if (!$row) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'Row tidak ditemukan']);
        }

        $vals = (new IndicatorValueModel())
            ->select('year, quarter, month')
            ->where('row_id', $rowId)
            ->where('region_id', $regionId)
            ->orderBy('year', 'ASC')
            ->orderBy('quarter', 'ASC')
            ->orderBy('month', 'ASC')
            ->findAll();

        $years = [];
        $qByY = [];
        $mByY = [];

        foreach ($vals as $v) {
            $y = (int) $v['year'];
            if (!in_array($y, $years, true)) $years[] = $y;
            if (!is_null($v['quarter'])) $qByY[$y][] = (int) $v['quarter'];
            if (!is_null($v['month']))   $mByY[$y][] = (int) $v['month'];
        }
        sort($years);
        foreach ($qByY as $y => &$qs) {
            $qs = array_values(array_unique($qs));
            sort($qs);
        }
        foreach ($mByY as $y => &$ms) {
            $ms = array_values(array_unique($ms));
            sort($ms);
        }

        $lastYear = count($years) ? end($years) : null;
        $defQ = ($lastYear && !empty($qByY[$lastYear])) ? end($qByY[$lastYear]) : null;
        $defM = ($lastYear && !empty($mByY[$lastYear])) ? end($mByY[$lastYear]) : null;

        return $this->response->setJSON([
            'ok'       => true,
            'timeline' => strtoupper($row['timeline']),
            'years'    => $years,
            'quartersByYear' => $qByY,
            'monthsByYear'   => $mByY,
            'defaults' => ['year' => $lastYear, 'quarter' => $defQ, 'month' => $defM],
        ]);
    }

    // Export .xlsx sederhana (Excel bisa buka)
    public function apiExportXlsx()
    {
        $jenis = (string)($this->request->getGet('jenis') ?? 'series');
        $multi = (int)($this->request->getGet('multi') ?? 0) === 1; // ADDED

        if ($jenis === 'series') {
            $resp = $this->apiSeries();
            $payload = json_decode($resp->getBody(), true);
            if (!$payload['ok']) return $resp;

            // ===== MULTI DATASET (bar multi variabel) ===== // ADDED
            if ($multi && !empty($payload['datasets'])) {
                $headers = array_merge(['Label'], array_map(fn($d) => $d['name'], $payload['datasets']));
                $csv = implode(',', array_map(fn($h) => '"' . str_replace('"', '""', $h) . '"', $headers)) . "\n";
                foreach ($payload['labels'] as $i => $lab) {
                    $row = ['"' . str_replace('"', '""', $lab) . '"'];
                    foreach ($payload['datasets'] as $ds) {
                        $val = $ds['values'][$i] ?? '';
                        $row[] = (is_null($val) ? '' : $val);
                    }
                    $csv .= implode(',', $row) . "\n";
                }
                $filename = 'series_multi_' . date('Ymd_His') . '.csv';
                return $this->response->setHeader('Content-Type', 'text/csv')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setBody($csv);
            }

            // ===== SINGLE DATASET (mode lama) =====
            $csv = "Label,Value\n";
            foreach ($payload['labels'] as $i => $lab) {
                $val = $payload['values'][$i] ?? '';
                $csv .= '"' . str_replace('"', '""', $lab) . '",' . (is_null($val) ? '' : $val) . "\n";
            }
            $filename = 'series_' . date('Ymd_His') . '.csv';
            return $this->response->setHeader('Content-Type', 'text/csv')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($csv);
        } else {
            $resp = $this->apiProportion();
            $payload = json_decode($resp->getBody(), true);
            if (!$payload['ok']) return $resp;

            $csv = "Kategori,Value\n";
            foreach ($payload['labels'] as $i => $lab) {
                $val = $payload['values'][$i] ?? '';
                $csv .= '"' . str_replace('"', '""', $lab) . '",' . (is_null($val) ? '' : $val) . "\n";
            }
            $filename = 'proportion_' . date('Ymd_His') . '.csv';
            return $this->response->setHeader('Content-Type', 'text/csv')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($csv);
        }
    }
}
