<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class IndicatorsSeeder extends Seeder
{
    public function run()
    {
        // ambil Kota Tegal sbg default
        $region = $this->db->table('regions')->where('name', 'Kota Tegal')->get()->getRowArray();
        if (!$region) return;

        // 1 indikator contoh
        $this->db->table('indicators')->insert([
            'region_id' => $region['id'],
            'name'      => 'Penduduk dan Kepadatan',
            'code'      => 'KEPEND',
        ]);
        $indicatorId = $this->db->insertID();

        // 2 subindikator: satu "single-yearly", satu "proporsi-monthly"
        $this->db->table('indicator_rows')->insertBatch([
            [
                'indicator_id' => $indicatorId,
                'subindikator' => 'Jumlah Penduduk',
                'timeline' => 'yearly',
                'data_type' => 'timeseries',
                'unit' => 'jiwa',
                'sort_order' => 1,
            ],
            [
                'indicator_id' => $indicatorId,
                'subindikator' => 'Angkatan Kerja (Proporsi)',
                'timeline' => 'monthly',
                'data_type' => 'proporsi', 
                'unit' => '%',
                'sort_order' => 2,
            ],
        ]);

        // Vars untuk subindikator proporsi
        $rowProp = $this->db->table('indicator_rows')->where('indicator_id', $indicatorId)->where('data_type', 'proporsi')->get()->getRowArray();
        if ($rowProp) {
            $this->db->table('indicator_row_vars')->insertBatch([
                ['row_id' => $rowProp['id'], 'name' => 'Bekerja', 'sort_order' => 1],
                ['row_id' => $rowProp['id'], 'name' => 'Penganggur', 'sort_order' => 2],
            ]);
        }
    }
}
