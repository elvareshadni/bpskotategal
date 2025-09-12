<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IndicatorModel;
use App\Models\IndicatorRowModel;
use App\Models\IndicatorValueModel;
use App\Models\RegionModel;

class Indicators extends BaseController
{
    public function index()
    {
        $key = strtoupper($this->request->getGet('key') ?? '');
        if ($key === '') {
            return $this->respondOk([], [], 'Missing ?key'); // tetap ok=true agar frontend tidak error
        }

        $indicator = (new IndicatorModel())->where('code', $key)->first();
        if (!$indicator) {
            return $this->respondOk([], [], 'Unknown key');
        }

        // pilih region: ?region_id=ID (opsional). Jika tidak ada → pakai is_default=1 atau region pertama.
        $regionId = (int) ($this->request->getGet('region_id') ?? 0);
        $regionModel = new RegionModel();
        if ($regionId > 0) {
            $region = $regionModel->find($regionId);
        } else {
            $region = $regionModel->where('is_default',1)->first() ?? $regionModel->orderBy('id','asc')->first();
        }
        if (!$region) {
            return $this->respondOk([], [], 'No region');
        }
        $regionId = (int)$region['id'];

        // Ambil semua baris (Kelompok/SubIndikator) untuk indikator ini
        $rowModel = new IndicatorRowModel();
        $rowsMeta = $rowModel->where('indicator_id', (int)$indicator['id'])
            ->orderBy('sort_order','asc')->orderBy('id','asc')->findAll();
        if (!$rowsMeta) {
            return $this->respondOk([], [], 'No rows');
        }

        // Ambil semua nilai untuk region ini
        $valModel = new IndicatorValueModel();
        $vals = $valModel->select('row_id, year, value')
            ->where('region_id', $regionId)
            ->whereIn('row_id', array_column($rowsMeta, 'id'))
            ->orderBy('year','asc')
            ->findAll();

        // Tahun unik (urut)
        $years = [];
        foreach ($vals as $v) {
            $y = (int)$v['year'];
            if ($y && !in_array($y, $years, true)) $years[] = $y;
        }
        sort($years);

        // Jika belum ada data sama sekali, tetap kirim kolom dasar saja
        $columns = array_merge(['Kelompok','SubIndikator'], array_map('strval', $years));

        // index nilai: [row_id][year] => value
        $map = [];
        foreach ($vals as $v) {
            $rid = (int)$v['row_id']; $y = (int)$v['year'];
            $map[$rid][$y] = is_null($v['value']) ? null : (float)$v['value'];
        }

        // bentuk rows siap pivot
        $outRows = [];
        foreach ($rowsMeta as $rm) {
            $row = [
                'Kelompok'     => (string)($rm['kelompok'] ?? ''),
                'SubIndikator' => (string)($rm['subindikator'] ?? ''),
            ];
            foreach ($years as $y) {
                // NULL aman; frontend kamu sudah handle dengan toNumber()
                $row[(string)$y] = $map[$rm['id']][$y] ?? null;
            }
            $outRows[] = $row;
        }

        return $this->response->setJSON([
            'ok'      => true,
            'source'  => $key,
            'region'  => $region['name'],
            'columns' => $columns,
            'rows'    => $outRows,
        ]);
    }

    private function respondOk(array $columns, array $rows, string $note = '')
    {
        return $this->response->setJSON([
            'ok'      => true,          // <- tetap true agar frontend tidak throw Error
            'note'    => $note,
            'columns' => $columns,
            'rows'    => $rows,
        ]);
    }
}
