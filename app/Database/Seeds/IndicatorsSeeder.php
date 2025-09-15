<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class IndicatorsSeeder extends Seeder
{
    private function idOfRegion(string $codeBps)
    {
        return $this->db->table('regions')->where('code_bps', $codeBps)->get()->getRow('id');
    }

    private function addIndicator(int $regionId, string $name, ?string $code = null): int
    {
        $row = [
            'region_id'  => $regionId,
            'name'       => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($code !== null) $row['code'] = $code; // aman bila kolom code ada
        $this->db->table('indicators')->insert($row);
        return (int) $this->db->insertID();
    }

    // E:\laragon\www\bpskotategal\app\Database\Seeds\IndicatorsSeeder.php

    private function addSingleVar(int $rowId, string $name = 'Jumlah'): int
    {
        $this->db->table('indicator_row_vars')->insert([
            'row_id'     => $rowId,
            'name'       => $name,
            'sort_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return (int) $this->db->insertID();
    }

    private function addValuesSingleVar(int $rowId, int $regionId, int $varId, array $yearVal): void
    {
        foreach ($yearVal as $e) {
            $this->db->table('indicator_values')->insert([
                'row_id'    => $rowId,
                'region_id' => $regionId,
                'var_id'    => $varId,
                'year'      => (int)$e['year'],
                'quarter'   => null,
                'month'     => null,
                'value'     => is_null($e['value']) ? null : (float)$e['value'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function addValuesQuarterlyVar(int $rowId, int $regionId, int $varId, int $year, array $qToVal): void
    {
        foreach ($qToVal as $q => $val) {
            $this->db->table('indicator_values')->insert([
                'row_id'    => $rowId,
                'region_id' => $regionId,
                'var_id'    => $varId,
                'year'      => $year,
                'quarter'   => $q,
                'month'     => null,
                'value'     => is_null($val) ? null : (float)$val,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function addValuesMonthlyVar(int $rowId, int $regionId, int $varId, int $year, array $mToVal): void
    {
        foreach ($mToVal as $m => $val) {
            $this->db->table('indicator_values')->insert([
                'row_id'    => $rowId,
                'region_id' => $regionId,
                'var_id'    => $varId,
                'year'      => $year,
                'quarter'   => null,
                'month'     => $m,
                'value'     => is_null($val) ? null : (float)$val,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function addRow(int $indicatorId, string $sub, string $timeline, string $dtype, ?string $unit, int $order = 1, ?string $interpretasi = null): int
    {
        $this->db->table('indicator_rows')->insert([
            'indicator_id' => $indicatorId,
            'subindikator' => $sub,
            'timeline'     => $timeline,           // yearly|quarterly|monthly
            'data_type'    => $dtype,              // timeseries|jumlah_kategori|proporsi
            'unit'         => $unit,               // boleh null
            'interpretasi' => $interpretasi,       // boleh null
            'sort_order'   => $order,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);
        return (int) $this->db->insertID();
    }

    private function addValuesSingle(int $rowId, int $regionId, array $yearVal)
    {
        // $yearVal: [ ['year'=>YYYY, 'value'=>float|int], ... ]
        foreach ($yearVal as $e) {
            $this->db->table('indicator_values')->insert([
                'row_id'    => $rowId,
                'region_id' => $regionId,
                'var_id'    => null,
                'year'      => (int)$e['year'],
                'quarter'   => null,
                'month'     => null,
                'value'     => is_null($e['value']) ? null : (float)$e['value'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function addValuesQuarterly(int $rowId, int $regionId, int $year, array $qToVal)
    {
        // $qToVal: [ 1=>val, 2=>val, 3=>val, 4=>val ]
        foreach ($qToVal as $q => $val) {
            $this->db->table('indicator_values')->insert([
                'row_id'    => $rowId,
                'region_id' => $regionId,
                'var_id'    => null,
                'year'      => $year,
                'quarter'   => $q,
                'month'     => null,
                'value'     => is_null($val) ? null : (float)$val,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function addValuesMonthly(int $rowId, int $regionId, int $year, array $mToVal)
    {
        // $mToVal: [ 1=>val, 2=>val, ..., 12=>val ]
        foreach ($mToVal as $m => $val) {
            $this->db->table('indicator_values')->insert([
                'row_id'    => $rowId,
                'region_id' => $regionId,
                'var_id'    => null,
                'year'      => $year,
                'quarter'   => null,
                'month'     => $m,
                'value'     => is_null($val) ? null : (float)$val,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function addProporsi(int $rowId, int $regionId, int $year, ?int $q, ?int $m, array $varNameToVal)
    {
        // Pastikan vars sudah ada & urut
        $vars = [];
        $order = 1;
        foreach ($varNameToVal as $name => $val) {
            $this->db->table('indicator_row_vars')->insert([
                'row_id'     => $rowId,
                'name'       => $name,
                'sort_order' => $order++,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $vars[] = [
                'id'   => (int)$this->db->insertID(),
                'name' => $name,
                'val'  => $val
            ];
        }

        foreach ($vars as $v) {
            $this->db->table('indicator_values')->insert([
                'row_id'    => $rowId,
                'region_id' => $regionId,
                'var_id'    => $v['id'],
                'year'      => $year,
                'quarter'   => $q,
                'month'     => $m,
                'value'     => is_null($v['val']) ? null : (float)$v['val'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function run()
    {
        // ========= Ambil ID Region =========
        $idTegal   = $this->idOfRegion('3372'); // Kota Tegal
        $idKab     = $this->idOfRegion('3328'); // Kabupaten Tegal (dipakai sedikit contoh)
        $idDummy   = $this->idOfRegion('3373'); // Kota Dummy

        // =====================================================
        // ===============  K O T A   T E G A L  ===============
        // Data dari spreadsheet 2019–2024 (hardcoded)
        // =====================================================

        // --- 1) Kependudukan
        $indKepend = $this->addIndicator($idTegal, 'Kependudukan', 'KEPEND');
        // Jumlah Penduduk (jiwa)
        $row = $this->addRow($indKepend, 'Jumlah Penduduk', 'yearly', 'timeseries', 'jiwa', 1);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 249905],
            ['year' => 2020, 'value' => 273048],
            ['year' => 2021, 'value' => 276399],
            ['year' => 2022, 'value' => 279641],
            ['year' => 2023, 'value' => 282781],
            ['year' => 2024, 'value' => 285843],
        ]);
        // -Laki-laki
        $row = $this->addRow($indKepend, 'Jumlah Penduduk Laki-laki', 'yearly', 'timeseries', 'jiwa', 2);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 123701],
            ['year' => 2020, 'value' => 137801],
            ['year' => 2021, 'value' => 139455],
            ['year' => 2022, 'value' => 141056],
            ['year' => 2023, 'value' => 142593],
            ['year' => 2024, 'value' => 144086],
        ]);
        // -Perempuan
        $row = $this->addRow($indKepend, 'Jumlah Penduduk Perempuan', 'yearly', 'timeseries', 'jiwa', 3);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 126204],
            ['year' => 2020, 'value' => 135247],
            ['year' => 2021, 'value' => 136944],
            ['year' => 2022, 'value' => 138585],
            ['year' => 2023, 'value' => 140188],
            ['year' => 2024, 'value' => 141757],
        ]);
        // Angka Ketergantungan
        $row = $this->addRow($indKepend, 'Angka Ketergantungan', 'yearly', 'timeseries', null, 4);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 42.88],
            ['year' => 2020, 'value' => 41.90],
            ['year' => 2021, 'value' => 42.25],
            ['year' => 2022, 'value' => 42.61],
            ['year' => 2023, 'value' => 42.98],
            ['year' => 2024, 'value' => 43.37],
        ]);
        // Kepadatan Penduduk (Jiwa/Km2)
        $row = $this->addRow($indKepend, 'Kepadatan Penduduk', 'yearly', 'timeseries', 'Jiwa/Km2', 5);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 6298.01],
            ['year' => 2020, 'value' => 6958.41],
            ['year' => 2021, 'value' => 7043.81],
            ['year' => 2022, 'value' => 7126.43],
            ['year' => 2023, 'value' => 7206.45],
            ['year' => 2024, 'value' => 7284.48],
        ]);
        // Sex Ratio
        $row = $this->addRow($indKepend, 'Sex Ratio', 'yearly', 'timeseries', null, 6);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 98.02],
            ['year' => 2020, 'value' => 101.89],
            ['year' => 2021, 'value' => 101.83],
            ['year' => 2022, 'value' => 101.78],
            ['year' => 2023, 'value' => 101.72],
            ['year' => 2024, 'value' => 101.64],
        ]);

        // --- 2) Kemiskinan
        $indKemiskinan = $this->addIndicator($idTegal, 'Kemiskinan', 'KMISK');
        $row = $this->addRow($indKemiskinan, 'Persentase Penduduk Miskin', 'yearly', 'timeseries', '%', 1);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 7.47],
            ['year' => 2020, 'value' => 7.80],
            ['year' => 2021, 'value' => 8.12],
            ['year' => 2022, 'value' => 7.91],
            ['year' => 2023, 'value' => 7.68],
            ['year' => 2024, 'value' => 7.64],
        ]);
        $row = $this->addRow($indKemiskinan, 'Indeks Kedalaman Kemiskinan (P1)', 'yearly', 'timeseries', null, 2);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 1.15],
            ['year' => 2020, 'value' => 1.38],
            ['year' => 2021, 'value' => 1.04],
            ['year' => 2022, 'value' => 1.15],
            ['year' => 2023, 'value' => 0.86],
            ['year' => 2024, 'value' => 1.34],
        ]);
        $row = $this->addRow($indKemiskinan, 'Indeks Keparahan Kemiskinan (P2)', 'yearly', 'timeseries', null, 3);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 0.24],
            ['year' => 2020, 'value' => 0.36],
            ['year' => 2021, 'value' => 0.24],
            ['year' => 2022, 'value' => 0.28],
            ['year' => 2023, 'value' => 0.13],
            ['year' => 2024, 'value' => 0.32],
        ]);
        $row = $this->addRow($indKemiskinan, 'Garis Kemiskinan', 'yearly', 'timeseries', 'Rupiah/Kapita/Bulan', 4);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 465047],
            ['year' => 2020, 'value' => 502031],
            ['year' => 2021, 'value' => 523413],
            ['year' => 2022, 'value' => 565826],
            ['year' => 2023, 'value' => 623617],
            ['year' => 2024, 'value' => 664962],
        ]);

        // --- 3) Ketenagakerjaan
        $indNaker = $this->addIndicator($idTegal, 'Ketenagakerjaan', 'NAKER');
        $row = $this->addRow($indNaker, 'Tingkat Pengangguran Terbuka (TPT)', 'yearly', 'timeseries', '%', 1);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 8.08],
            ['year' => 2020, 'value' => 8.40],
            ['year' => 2021, 'value' => 8.25],
            ['year' => 2022, 'value' => 6.68],
            ['year' => 2023, 'value' => 6.05],
            ['year' => 2024, 'value' => 5.88],
        ]);
        $row = $this->addRow($indNaker, 'Tingkat Partisipasi Angkatan Kerja (TPAK)', 'yearly', 'timeseries', '%', 2);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 69.61],
            ['year' => 2020, 'value' => 69.32],
            ['year' => 2021, 'value' => 68.25],
            ['year' => 2022, 'value' => 68.60],
            ['year' => 2023, 'value' => 66.64],
            ['year' => 2024, 'value' => 69.61],
        ]);

        // --- 4) Perekonomian
        $indEko = $this->addIndicator($idTegal, 'Perekonomian', 'EKO');
        $row = $this->addRow($indEko, 'PDRB per Kapita (ADHB)', 'yearly', 'timeseries', 'Juta Rupiah', 1);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 61.47],
            ['year' => 2020, 'value' => 55.72],
            ['year' => 2021, 'value' => 57.95],
            ['year' => 2022, 'value' => 63.22],
            ['year' => 2023, 'value' => 67.76],
        ]);
        $row = $this->addRow($indEko, 'Laju Pertumbuhan Ekonomi (y-on-y)', 'yearly', 'timeseries', '%', 2);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 5.77],
            ['year' => 2020, 'value' => -2.29],
            ['year' => 2021, 'value' => 3.12],
            ['year' => 2022, 'value' => 5.16],
            ['year' => 2023, 'value' => 5.01],
        ]);

        // --- 5) Ketimpangan & Distribusi
        $indKetimp = $this->addIndicator($idTegal, 'Ketimpangan & Distribusi', 'KETIMP');
        $row = $this->addRow($indKetimp, 'Gini Ratio', 'yearly', 'timeseries', null, 1);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2021, 'value' => 0.38],
            ['year' => 2022, 'value' => 0.37],
            ['year' => 2023, 'value' => 0.38],
        ]);
        // Proporsi Distribusi Pendapatan (pakai 2023)
        $row = $this->addRow($indKetimp, 'Distribusi Pendapatan (Proporsi)', 'yearly', 'proporsi', '%', 2);
        $this->addProporsi($row, $idTegal, 2023, null, null, [
            '40% bawah' => 18.43,
            '40% tengah' => 35.56,
            '20% atas'  => 46.01,
        ]);

        // --- 6) Pembangunan Manusia
        $indIPM = $this->addIndicator($idTegal, 'Dimensi IPM', 'IPM');
        $row = $this->addRow($indIPM, 'Umur Harapan Hidup', 'yearly', 'timeseries', 'tahun', 1);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 74.34],
            ['year' => 2020, 'value' => 74.46],
            ['year' => 2021, 'value' => 74.54],
            ['year' => 2022, 'value' => 74.64],
            ['year' => 2023, 'value' => 74.84],
            ['year' => 2024, 'value' => 75.01],
        ]);
        $row = $this->addRow($indIPM, 'Harapan Lama Sekolah', 'yearly', 'timeseries', 'tahun', 2);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 13.04],
            ['year' => 2020, 'value' => 13.05],
            ['year' => 2021, 'value' => 13.07],
            ['year' => 2022, 'value' => 13.08],
            ['year' => 2023, 'value' => 13.18],
            ['year' => 2024, 'value' => 13.25],
        ]);
        $row = $this->addRow($indIPM, 'Rata-rata Lama Sekolah (RLS)', 'yearly', 'timeseries', 'tahun', 3);
        $this->addValuesSingle($row, $idTegal, [
            ['year' => 2019, 'value' => 8.31],
            ['year' => 2020, 'value' => 8.51],
            ['year' => 2021, 'value' => 8.73],
            ['year' => 2022, 'value' => 9.00],
            ['year' => 2023, 'value' => 9.24],
            ['year' => 2024, 'value' => 9.28],
        ]);

        // (Opsional) contoh 1 indikator di Kabupaten Tegal agar panel bisa dipilih silang
        $indKab = $this->addIndicator($idKab, 'PDRB Atas Dasar Harga Berlaku (Dummy Kab.)', 'PDRB_KAB');
        $row = $this->addRow($indKab, 'Nilai PDRB', 'yearly', 'jumlah_kategori', 'Miliar Rupiah', 1);
        $this->addValuesSingle($row, $idKab, [
            ['year' => 2019, 'value' => 14500],
            ['year' => 2020, 'value' => 15050],
            ['year' => 2021, 'value' => 16010],
            ['year' => 2022, 'value' => 17500],
            ['year' => 2023, 'value' => 18900],
            ['year' => 2024, 'value' => 20100],
        ]);

        // =====================================================
        // ===================  K O T A  D U M M Y  ============
        // Data acak lengkap: yearly, quarterly, monthly, proporsi
        // =====================================================

        // --- A) Timeseries (Tahunan) → Line
        $indA = $this->addIndicator($idDummy, 'Contoh Timeseries Tahunan', 'D_Y_LINE');
        $row = $this->addRow($indA, 'Produksi Beras', 'yearly', 'timeseries', 'ton', 1);
        $this->addValuesSingle($row, $idDummy, [
            ['year' => 2019, 'value' => 12000],
            ['year' => 2020, 'value' => 11800],
            ['year' => 2021, 'value' => 12600],
            ['year' => 2022, 'value' => 13050],
            ['year' => 2023, 'value' => 12870],
            ['year' => 2024, 'value' => 13540],
        ]);

        // --- B) Timeseries (Triwulanan) → Line (tiap tahun)
        $indB = $this->addIndicator($idDummy, 'Contoh Timeseries Triwulanan', 'D_Q_LINE');
        $rowQ = $this->addRow($indB, 'Produksi Ikan (Triwulanan)', 'quarterly', 'timeseries', 'ton', 1);
        // seed 2023 & 2024 agar filter tahun bisa dicoba
        $this->addValuesQuarterly($rowQ, $idDummy, 2023, [1 => 320, 2 => 410, 3 => 380, 4 => 450]);
        $this->addValuesQuarterly($rowQ, $idDummy, 2024, [1 => 360, 2 => 430, 3 => 405, 4 => 470]);

        // --- C) Timeseries (Bulanan) → Line
        $indC = $this->addIndicator($idDummy, 'Contoh Timeseries Bulanan', 'D_M_LINE');
        $rowM = $this->addRow($indC, 'Kunjungan Wisatawan (Bulanan)', 'monthly', 'timeseries', 'orang', 1);
        $this->addValuesMonthly($rowM, $idDummy, 2024, [
            1 => 1200,
            2 => 1280,
            3 => 1500,
            4 => 1600,
            5 => 1750,
            6 => 1800,
            7 => 1900,
            8 => 2100,
            9 => 1950,
            10 => 1850,
            11 => 1700,
            12 => 2200
        ]);

        // --- D) Jumlah Kategori (Tahunan) → Bar (secara data sama single series)
        $indD = $this->addIndicator($idDummy, 'Contoh Jumlah Kategori Tahunan', 'D_Y_BAR');
        $rowD = $this->addRow($indD, 'Jumlah UMKM Terdaftar', 'yearly', 'jumlah_kategori', 'unit', 1);
        $this->addValuesSingle($rowD, $idDummy, [
            ['year' => 2019, 'value' => 5400],
            ['year' => 2020, 'value' => 5600],
            ['year' => 2021, 'value' => 5900],
            ['year' => 2022, 'value' => 6200],
            ['year' => 2023, 'value' => 6500],
            ['year' => 2024, 'value' => 7000],
        ]);

        // --- E) Proporsi (Tahunan) → Pie
        $indE = $this->addIndicator($idDummy, 'Contoh Proporsi Tahunan', 'D_Y_PIE');
        $rowE = $this->addRow($indE, 'Pangsa Sektor Ekonomi', 'yearly', 'proporsi', '%', 1);
        $this->addProporsi($rowE, $idDummy, 2024, null, null, [
            'Pertanian' => 18.5,
            'Industri' => 36.2,
            'Jasa' => 45.3
        ]);

        // --- F) Proporsi (Triwulanan) → Pie (pilih quarter)
        $indF = $this->addIndicator($idDummy, 'Contoh Proporsi Triwulanan', 'D_Q_PIE');
        $rowF = $this->addRow($indF, 'Struktur Pengeluaran RT', 'quarterly', 'proporsi', '%', 1);
        // Q1 & Q4 2024
        $this->addProporsi($rowF, $idDummy, 2024, 1, null, ['Pangan' => 52.0, 'Non-Pangan' => 48.0]);
        $this->addProporsi($rowF, $idDummy, 2024, 4, null, ['Pangan' => 49.5, 'Non-Pangan' => 50.5]);

        // --- G) Proporsi (Bulanan) → Pie (pilih bulan)
        $indG = $this->addIndicator($idDummy, 'Contoh Proporsi Bulanan', 'D_M_PIE');
        $rowG = $this->addRow($indG, 'Moda Transportasi', 'monthly', 'proporsi', '%', 1);
        // Jan & Des 2024
        $this->addProporsi($rowG, $idDummy, 2024, null, 1,  ['Darat' => 70, 'Laut' => 20, 'Udara' => 10]);
        $this->addProporsi($rowG, $idDummy, 2024, null, 12, ['Darat' => 65, 'Laut' => 22, 'Udara' => 13]);

        // ===== Tambahan dataset uji coba — KOTA DUMMY =====

        // BAR (TRIWULAN) → jumlah_kategori + quarterly
        $indQB = $this->addIndicator($idDummy, 'Contoh Bar Triwulanan', 'D_Q_BAR');
        $rowQB = $this->addRow($indQB, 'Produksi Hortikultura (Triwulanan)', 'quarterly', 'jumlah_kategori', 'ton', 1);
        // 2023 & 2024 supaya bisa gonta-ganti tahun
        $this->addValuesQuarterly($rowQB, $idDummy, 2023, [1 => 820, 2 => 900, 3 => 880, 4 => 950]);
        $this->addValuesQuarterly($rowQB, $idDummy, 2024, [1 => 860, 2 => 940, 3 => 905, 4 => 980]);

        // BAR (BULANAN) → jumlah_kategori + monthly
        $indMB = $this->addIndicator($idDummy, 'Contoh Bar Bulanan', 'D_M_BAR');
        $rowMB = $this->addRow($indMB, 'Penjualan Ritel (Bulanan)', 'monthly', 'jumlah_kategori', 'Miliar Rupiah', 1);
        $this->addValuesMonthly($rowMB, $idDummy, 2024, [
            1 => 85,
            2 => 88,
            3 => 92,
            4 => 95,
            5 => 98,
            6 => 102,
            7 => 110,
            8 => 120,
            9 => 108,
            10 => 104,
            11 => 100,
            12 => 130
        ]);

        // LINE (TRIWULAN) lain (variasi pola) → timeseries + quarterly
        $indQL = $this->addIndicator($idDummy, 'Contoh Line Triwulanan 2', 'D_Q_LINE_2');
        $rowQL = $this->addRow($indQL, 'Indeks Aktivitas Logistik (Triwulanan)', 'quarterly', 'timeseries', 'indeks', 1);
        $this->addValuesQuarterly($rowQL, $idDummy, 2023, [1 => 99.2, 2 => 101.5, 3 => 100.1, 4 => 102.3]);
        $this->addValuesQuarterly($rowQL, $idDummy, 2024, [1 => 100.7, 2 => 103.0, 3 => 101.2, 4 => 104.1]);

        // LINE (BULANAN) lain → timeseries + monthly
        $indML = $this->addIndicator($idDummy, 'Contoh Line Bulanan 2', 'D_M_LINE_2');
        $rowML = $this->addRow($indML, 'Indeks Harga Grosir (Bulanan)', 'monthly', 'timeseries', 'indeks', 1);
        $this->addValuesMonthly($rowML, $idDummy, 2024, [
            1 => 101.1,
            2 => 101.3,
            3 => 101.9,
            4 => 102.2,
            5 => 102.5,
            6 => 103.0,
            7 => 103.4,
            8 => 103.9,
            9 => 103.6,
            10 => 103.2,
            11 => 103.1,
            12 => 104.0
        ]);

        // PIE (TRIWULAN) tambahan dengan 3 kategori
        $indQP = $this->addIndicator($idDummy, 'Contoh Proporsi Triwulanan 2', 'D_Q_PIE_2');
        $rowQP = $this->addRow($indQP, 'Pangsa Kunjungan Objek Wisata', 'quarterly', 'proporsi', '%', 1);
        // Tahun 2024 Q2 & Q3
        $this->addProporsi($rowQP, $idDummy, 2024, 2, null, ['Pantai' => 44.0, 'Kota' => 33.0, 'Pegunungan' => 23.0]);
        $this->addProporsi($rowQP, $idDummy, 2024, 3, null, ['Pantai' => 40.0, 'Kota' => 35.0, 'Pegunungan' => 25.0]);

        // PIE (BULANAN) tambahan dengan 4 kategori
        $indMP = $this->addIndicator($idDummy, 'Contoh Proporsi Bulanan 2', 'D_M_PIE_2');
        $rowMP = $this->addRow($indMP, 'Moda Perjalanan Bulanan', 'monthly', 'proporsi', '%', 1);
        // 2024—Maret (03) & Agustus (08)
        $this->addProporsi($rowMP, $idDummy, 2024, null, 3,  ['Darat' => 62, 'Laut' => 14, 'Udara' => 20, 'Lainnya' => 4]);
        $this->addProporsi($rowMP, $idDummy, 2024, null, 8,  ['Darat' => 58, 'Laut' => 18, 'Udara' => 21, 'Lainnya' => 3]);

        // BAR (TAHUNAN) tambahan → jumlah_kategori + yearly
        $indYB2 = $this->addIndicator($idDummy, 'Contoh Bar Tahunan 2', 'D_Y_BAR_2');
        $rowYB2 = $this->addRow($indYB2, 'Jumlah Koperasi Aktif', 'yearly', 'jumlah_kategori', 'unit', 1);
        $this->addValuesSingle($rowYB2, $idDummy, [
            ['year' => 2019, 'value' => 310],
            ['year' => 2020, 'value' => 295],
            ['year' => 2021, 'value' => 305],
            ['year' => 2022, 'value' => 330],
            ['year' => 2023, 'value' => 345],
            ['year' => 2024, 'value' => 360],
        ]);

        // --- D) Jumlah Kategori (Tahunan) → Bar
        $indD = $this->addIndicator($idDummy, 'Contoh Jumlah Kategori Tahunan', 'D_Y_BAR');
        $rowD = $this->addRow($indD, 'Jumlah UMKM Terdaftar', 'yearly', 'jumlah_kategori', 'unit', 1);
        $varD = $this->addSingleVar($rowD, 'Jumlah'); // <— WAJIB ada var
        $this->addValuesSingleVar($rowD, $idDummy, $varD, [
            ['year' => 2019, 'value' => 5400],
            ['year' => 2020, 'value' => 5600],
            ['year' => 2021, 'value' => 5900],
            ['year' => 2022, 'value' => 6200],
            ['year' => 2023, 'value' => 6500],
            ['year' => 2024, 'value' => 7000],
        ]);

        // --- BAR (TRIWULAN)
        $indQB = $this->addIndicator($idDummy, 'Contoh Bar Triwulanan', 'D_Q_BAR');
        $rowQB = $this->addRow($indQB, 'Produksi Hortikultura (Triwulanan)', 'quarterly', 'jumlah_kategori', 'ton', 1);
        $varQB = $this->addSingleVar($rowQB, 'Jumlah');
        $this->addValuesQuarterlyVar($rowQB, $idDummy, $varQB, 2023, [1 => 820, 2 => 900, 3 => 880, 4 => 950]);
        $this->addValuesQuarterlyVar($rowQB, $idDummy, $varQB, 2024, [1 => 860, 2 => 940, 3 => 905, 4 => 980]);

        // --- BAR (BULANAN)
        $indMB = $this->addIndicator($idDummy, 'Contoh Bar Bulanan', 'D_M_BAR');
        $rowMB = $this->addRow($indMB, 'Penjualan Ritel (Bulanan)', 'monthly', 'jumlah_kategori', 'Miliar Rupiah', 1);
        $varMB = $this->addSingleVar($rowMB, 'Jumlah');
        $this->addValuesMonthlyVar($rowMB, $idDummy, $varMB, 2024, [
            1 => 85,
            2 => 88,
            3 => 92,
            4 => 95,
            5 => 98,
            6 => 102,
            7 => 110,
            8 => 120,
            9 => 108,
            10 => 104,
            11 => 100,
            12 => 130
        ]);
    }
}
