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
    // --- API kecil awal (boleh dipertahankan) ---
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
                'timeline' => strtoupper($r['timeline']),            // YEARLY|QUARTERLY|MONTHLY
                'data_type' => strtoupper($r['data_type']),          // TIMESERIES|JUMLAH_KATEGORI|PROPORSI
                'unit' => $r['unit'],
                'interpretasi' => $r['interpretasi'],
            ];
        }, $rows);

        return $this->response->setJSON(['ok' => true, 'rows' => $out]);
    }

    // SINGLE series (timeseries & jumlah_kategori(bisa bar tahunan))
    public function apiSeries()
    {
        $rowId    = (int)($this->request->getGet('row_id') ?? 0);
        $regionId = (int)($this->request->getGet('region_id') ?? 0);
        $window   = (string)($this->request->getGet('window') ?? 'all');
        $year     = (int)($this->request->getGet('year') ?? 0);      // untuk quarterly/monthly
        $quarter  = (int)($this->request->getGet('quarter') ?? 0);
        $month    = (int)($this->request->getGet('month') ?? 0);

        if ($rowId <= 0 || $regionId <= 0) return $this->response->setJSON(['ok' => false, 'error' => 'row_id & region_id required']);

        $row = (new IndicatorRowModel())->find($rowId);
        if (!$row) return $this->response->setJSON(['ok' => false, 'error' => 'row not found']);

        $timeline = $row['timeline']; // yearly|quarterly|monthly
        $valM = new IndicatorValueModel();
        $builder = $valM->where('row_id', $rowId)->where('region_id', $regionId)->where('var_id', null);

        // filter rentang
        if ($timeline === 'yearly') {
            if ($window === 'last3' || $window === 'last5') {
                $all = $valM->where('row_id', $rowId)->where('region_id', $regionId)->where('var_id', null)->select('year')->orderBy('year', 'ASC')->findAll();
                $years = array_values(array_unique(array_map(fn($r) => (int)$r['year'], $all)));
                $take = $window === 'last3' ? 3 : 5;
                $years = array_slice($years, max(0, count($years) - $take));
                if ($years) {
                    $builder->whereIn('year', $years);
                }
            } elseif ($year > 0) {
                $builder->where('year', $year);
            }
            $builder->orderBy('year', 'ASC');
        } elseif ($timeline === 'quarterly') {
            $y = $year ?: (int)date('Y');
            $builder->where('year', $y)->orderBy('quarter', 'ASC');
        } else { // monthly
            $y = $year ?: (int)date('Y');
            $builder->where('year', $y)->orderBy('month', 'ASC');
        }

        $vals = $builder->findAll();

        $labels = [];
        $values = [];
        if ($timeline === 'yearly') {
            $map = [];
            foreach ($vals as $v) {
                $map[(int)$v['year']] = is_null($v['value']) ? null : (float)$v['value'];
            }
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
            'ok' => true,
            'labels' => $labels,
            'values' => $values,
            'meta' => [
                'timeline' => strtoupper($timeline),
                'unit' => $row['unit'],
                'desc' => $row['subindikator'],
                'interpretasi' => $row['interpretasi']
            ]
        ]);
    }

    // PROPORSI: ambil label dari indicator_row_vars & nilai sesuai periode
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
        foreach ($vals as $v) {
            $map[(int)$v['var_id']] = is_null($v['value']) ? null : (float)$v['value'];
        }

        $data = [];
        foreach ($varIds as $vid) {
            $data[] = $map[$vid] ?? null;
        }

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

    // Export data yang sedang dibuka (labels+values) -> xlsx sederhana
    public function apiExportXlsx()
    {
        // jenis=series|proportion
        $jenis = (string)($this->request->getGet('jenis') ?? 'series');
        if ($jenis === 'series') {
            // proxy ke apiSeries untuk ambil data
            $resp = $this->apiSeries();
            $payload = json_decode($resp->getBody(), true);
            if (!$payload['ok']) return $resp;

            // bentuk CSV lalu force download .xlsx (simple)
            $csv = "Label,Value\n";
            foreach ($payload['labels'] as $i => $lab) {
                $val = $payload['values'][$i] ?? '';
                $csv .= '"' . str_replace('"', '""', $lab) . '",' . (is_null($val) ? '' : $val) . "\n";
            }
            $filename = 'series_' . date('Ymd_His') . '.csv'; // CSV (bisa dibuka Excel)
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
