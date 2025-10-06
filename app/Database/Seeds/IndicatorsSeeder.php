<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class IndicatorsSeeder extends Seeder
{
    /* =========================
     * Util & Guard Functions
     * ========================= */

    private function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    /** Pastikan region ada, kalau belum dibuat. */
    private function ensureRegion(string $codeBps, string $name, int $isDefault = 0): int
    {
        $row = $this->db->table('regions')->where('code_bps', $codeBps)->get()->getRowArray();
        if ($row) return (int)$row['id'];

        $this->db->table('regions')->insert([
            'code_bps'   => $codeBps,
            'name'       => $name,
            'is_default' => $isDefault,
            'created_at' => $this->now(),
            'updated_at' => $this->now(),
        ]);
        return (int)$this->db->insertID();
    }

    /** Hapus indikator (beserta rows/vars/values) berdasarkan kode unik indikator. */
    private function wipeIndicatorByCode(?string $code): void
    {
        if (!$code) return;
        $inds = $this->db->table('indicators')->where('code', $code)->get()->getResultArray();
        foreach ($inds as $ind) {
            $rows = $this->db->table('indicator_rows')->where('indicator_id', $ind['id'])->get()->getResultArray();
            $rowIds = array_map(fn($r) => (int)$r['id'], $rows);
            if (!empty($rowIds)) {
                $this->db->table('indicator_values')->whereIn('row_id', $rowIds)->delete();
                $this->db->table('indicator_row_vars')->whereIn('row_id', $rowIds)->delete();
                $this->db->table('indicator_rows')->whereIn('id', $rowIds)->delete();
            }
            $this->db->table('indicators')->where('id', $ind['id'])->delete();
        }
    }

    /** Buat indikator baru (hapus yang lama jika sama code). */
    private function addIndicator(int $regionId, string $name, ?string $code = null): int
    {
        if ($code) $this->wipeIndicatorByCode($code);

        $row = [
            'region_id'  => $regionId,
            'name'       => $name,
            'created_at' => $this->now(),
            'updated_at' => $this->now(),
        ];
        if ($code !== null) $row['code'] = $code;

        $this->db->table('indicators')->insert($row);
        return (int) $this->db->insertID();
    }

    private function addRow(
        int $indicatorId,
        string $sub,
        string $timeline,   // yearly|quarterly|monthly
        string $dtype,      // timeseries|jumlah_kategori|proporsi
        ?string $unit,
        int $order = 1,
        ?string $interpretasi = null
    ): int {
        $this->db->table('indicator_rows')->insert([
            'indicator_id' => $indicatorId,
            'subindikator' => $sub,
            'timeline'     => $timeline,
            'data_type'    => $dtype,
            'unit'         => $unit,
            'interpretasi' => $interpretasi,
            'sort_order'   => $order,
            'created_at'   => $this->now(),
            'updated_at'   => $this->now(),
        ]);
        return (int)$this->db->insertID();
    }

    /* ===== Vars (untuk jumlah_kategori / proporsi) ===== */

    private function addSingleVar(int $rowId, string $name = 'Jumlah'): int
    {
        $this->db->table('indicator_row_vars')->insert([
            'row_id'     => $rowId,
            'name'       => $name,
            'sort_order' => 1,
            'created_at' => $this->now(),
            'updated_at' => $this->now(),
        ]);
        return (int)$this->db->insertID();
    }

    private function addVars(int $rowId, array $names): array
    {
        $ids = [];
        $order = 1;
        foreach ($names as $n) {
            $this->db->table('indicator_row_vars')->insert([
                'row_id'     => $rowId,
                'name'       => $n,
                'sort_order' => $order++,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);
            $ids[] = (int)$this->db->insertID();
        }
        return $ids;
    }

    /* ===== Values (timeseries = var_id NULL) ===== */

    private function addValuesSingle(int $rowId, int $regionId, array $yearVal): void
    {
        foreach ($yearVal as $e) {
            $this->db->table('indicator_values')->insert([
                'row_id'     => $rowId,
                'region_id'  => $regionId,
                'var_id'     => null,
                'year'       => (int)$e['year'],
                'quarter'    => null,
                'month'      => null,
                'value'      => (float)$e['value'],
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);
        }
    }

    private function addValuesQuarterly(int $rowId, int $regionId, int $year, array $qToVal): void
    {
        foreach ($qToVal as $q => $val) {
            $this->db->table('indicator_values')->insert([
                'row_id'     => $rowId,
                'region_id'  => $regionId,
                'var_id'     => null,
                'year'       => $year,
                'quarter'    => (int)$q,
                'month'      => null,
                'value'      => (float)$val,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);
        }
    }

    private function addValuesMonthly(int $rowId, int $regionId, int $year, array $mToVal): void
    {
        foreach ($mToVal as $m => $val) {
            $this->db->table('indicator_values')->insert([
                'row_id'     => $rowId,
                'region_id'  => $regionId,
                'var_id'     => null,
                'year'       => $year,
                'quarter'    => null,
                'month'      => (int)$m,
                'value'      => (float)$val,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);
        }
    }

    /* ===== Values utk VAR (jumlah_kategori / proporsi) ===== */

    private function addValuesSingleVar(int $rowId, int $regionId, int $varId, array $yearVal): void
    {
        foreach ($yearVal as $e) {
            $this->db->table('indicator_values')->insert([
                'row_id'     => $rowId,
                'region_id'  => $regionId,
                'var_id'     => $varId,
                'year'       => (int)$e['year'],
                'quarter'    => null,
                'month'      => null,
                'value'      => (float)$e['value'],
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);
        }
    }

    private function addValuesQuarterlyVar(int $rowId, int $regionId, int $varId, int $year, array $qToVal): void
    {
        foreach ($qToVal as $q => $val) {
            $this->db->table('indicator_values')->insert([
                'row_id'     => $rowId,
                'region_id'  => $regionId,
                'var_id'     => $varId,
                'year'       => $year,
                'quarter'    => (int)$q,
                'month'      => null,
                'value'      => (float)$val,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);
        }
    }

    private function addValuesMonthlyVar(int $rowId, int $regionId, int $varId, int $year, array $mToVal): void
    {
        foreach ($mToVal as $m => $val) {
            $this->db->table('indicator_values')->insert([
                'row_id'     => $rowId,
                'region_id'  => $regionId,
                'var_id'     => $varId,
                'year'       => $year,
                'quarter'    => null,
                'month'      => (int)$m,
                'value'      => (float)$val,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);
        }
    }

    /** Proporsi: buat var per kategori lalu isi satu periode. */
    private function addProporsi(int $rowId, int $regionId, int $year, ?int $q, ?int $m, array $varNameToVal): void
    {
        // ambil var existing utk row ini (name => id)
        $exist = [];
        $vars = $this->db->table('indicator_row_vars')
            ->select('id, name')
            ->where('row_id', $rowId)
            ->get()->getResultArray();
        foreach ($vars as $v) $exist[$v['name']] = (int)$v['id'];

        // mulai sort_order dari jumlah yg sudah ada + 1
        $order = (int)$this->db->table('indicator_row_vars')->where('row_id', $rowId)->countAllResults() + 1;

        foreach ($varNameToVal as $name => $val) {
            // jika var blm ada => buat
            if (!isset($exist[$name])) {
                $this->db->table('indicator_row_vars')->insert([
                    'row_id'     => $rowId,
                    'name'       => $name,
                    'sort_order' => $order++,
                    'created_at' => $this->now(),
                    'updated_at' => $this->now(),
                ]);
                $exist[$name] = (int)$this->db->insertID();
            }
            $vid = $exist[$name];

            // masukkan value utk periode yg diminta
            $this->db->table('indicator_values')->insert([
                'row_id'     => $rowId,
                'region_id'  => $regionId,
                'var_id'     => $vid,
                'year'       => $year,
                'quarter'    => $q,
                'month'      => $m,
                'value'      => (float)$val,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);
        }
    }


    /* =========================
     * Seeder Content
     * ========================= */
    public function run()
    {
        // Pastikan region ada
        $idTegal = $this->ensureRegion('3372', 'Kota Tegal', 1);
        $idKab   = $this->ensureRegion('3328', 'Kabupaten Tegal', 0);
        $idDummy = $this->ensureRegion('3373', 'Kota Dummy', 0);

        /* =====================================================
         * ===============  K O T A   T E G A L  ===============
         * ===================================================== */

        // (1) Kependudukan
        $indKepend = $this->addIndicator($idTegal, 'Kependudukan', 'KEPEND');
        $r = $this->addRow($indKepend, 'Jumlah Penduduk', 'yearly', 'timeseries', 'jiwa', 1);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 249905],
            ['year' => 2020, 'value' => 273048],
            ['year' => 2021, 'value' => 276399],
            ['year' => 2022, 'value' => 279641],
            ['year' => 2023, 'value' => 282781],
            ['year' => 2024, 'value' => 285843],
        ]);
        $r = $this->addRow($indKepend, 'Jumlah Penduduk Laki-laki', 'yearly', 'timeseries', 'jiwa', 2);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 123701],
            ['year' => 2020, 'value' => 137801],
            ['year' => 2021, 'value' => 139455],
            ['year' => 2022, 'value' => 141056],
            ['year' => 2023, 'value' => 142593],
            ['year' => 2024, 'value' => 144086],
        ]);
        $r = $this->addRow($indKepend, 'Jumlah Penduduk Perempuan', 'yearly', 'timeseries', 'jiwa', 3);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 126204],
            ['year' => 2020, 'value' => 135247],
            ['year' => 2021, 'value' => 136944],
            ['year' => 2022, 'value' => 138585],
            ['year' => 2023, 'value' => 140188],
            ['year' => 2024, 'value' => 141757],
        ]);
        $r = $this->addRow($indKepend, 'Angka Ketergantungan', 'yearly', 'timeseries', null, 4);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 42.88],
            ['year' => 2020, 'value' => 41.90],
            ['year' => 2021, 'value' => 42.25],
            ['year' => 2022, 'value' => 42.61],
            ['year' => 2023, 'value' => 42.98],
            ['year' => 2024, 'value' => 43.37],
        ]);
        $r = $this->addRow($indKepend, 'Kepadatan Penduduk', 'yearly', 'timeseries', 'Jiwa/Km2', 5);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 6298.01],
            ['year' => 2020, 'value' => 6958.41],
            ['year' => 2021, 'value' => 7043.81],
            ['year' => 2022, 'value' => 7126.43],
            ['year' => 2023, 'value' => 7206.45],
            ['year' => 2024, 'value' => 7284.48],
        ]);
        $r = $this->addRow($indKepend, 'Sex Ratio', 'yearly', 'timeseries', null, 6);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 98.02],
            ['year' => 2020, 'value' => 101.89],
            ['year' => 2021, 'value' => 101.83],
            ['year' => 2022, 'value' => 101.78],
            ['year' => 2023, 'value' => 101.72],
            ['year' => 2024, 'value' => 101.64],
        ]);

        // (2) Kemiskinan
        $indKemiskinan = $this->addIndicator($idTegal, 'Kemiskinan', 'KMISK');
        $r = $this->addRow($indKemiskinan, 'Persentase Penduduk Miskin', 'yearly', 'timeseries', '%', 1, 'Lebih rendah lebih baik');
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 7.47],
            ['year' => 2020, 'value' => 7.80],
            ['year' => 2021, 'value' => 8.12],
            ['year' => 2022, 'value' => 7.91],
            ['year' => 2023, 'value' => 7.68],
            ['year' => 2024, 'value' => 7.64],
        ]);
        $r = $this->addRow($indKemiskinan, 'Indeks Kedalaman Kemiskinan (P1)', 'yearly', 'timeseries', null, 2, 'Lebih rendah lebih baik');
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 1.15],
            ['year' => 2020, 'value' => 1.38],
            ['year' => 2021, 'value' => 1.04],
            ['year' => 2022, 'value' => 1.15],
            ['year' => 2023, 'value' => 0.86],
            ['year' => 2024, 'value' => 1.34],
        ]);
        $r = $this->addRow($indKemiskinan, 'Indeks Keparahan Kemiskinan (P2)', 'yearly', 'timeseries', null, 3, 'Lebih rendah lebih baik');
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 0.24],
            ['year' => 2020, 'value' => 0.36],
            ['year' => 2021, 'value' => 0.24],
            ['year' => 2022, 'value' => 0.28],
            ['year' => 2023, 'value' => 0.13],
            ['year' => 2024, 'value' => 0.32],
        ]);
        $r = $this->addRow($indKemiskinan, 'Garis Kemiskinan', 'yearly', 'timeseries', 'Rupiah/Kapita/Bulan', 4);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 465047],
            ['year' => 2020, 'value' => 502031],
            ['year' => 2021, 'value' => 523413],
            ['year' => 2022, 'value' => 565826],
            ['year' => 2023, 'value' => 623617],
            ['year' => 2024, 'value' => 664962],
        ]);

        // (3) Ketenagakerjaan
        $indNaker = $this->addIndicator($idTegal, 'Ketenagakerjaan', 'NAKER');
        $r = $this->addRow($indNaker, 'Tingkat Pengangguran Terbuka (TPT)', 'yearly', 'timeseries', '%', 1, 'Lebih rendah lebih baik');
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 8.08],
            ['year' => 2020, 'value' => 8.40],
            ['year' => 2021, 'value' => 8.25],
            ['year' => 2022, 'value' => 6.68],
            ['year' => 2023, 'value' => 6.05],
            ['year' => 2024, 'value' => 5.88],
        ]);
        $r = $this->addRow($indNaker, 'Tingkat Partisipasi Angkatan Kerja (TPAK)', 'yearly', 'timeseries', '%', 2, 'Lebih tinggi lebih baik');
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 69.61],
            ['year' => 2020, 'value' => 69.32],
            ['year' => 2021, 'value' => 68.25],
            ['year' => 2022, 'value' => 68.60],
            ['year' => 2023, 'value' => 66.64],
            ['year' => 2024, 'value' => 69.61],
        ]);

        // (4) Perekonomian
        $indEko = $this->addIndicator($idTegal, 'Perekonomian', 'EKO');
        $r = $this->addRow($indEko, 'PDRB per Kapita (ADHB)', 'yearly', 'timeseries', 'Juta Rupiah', 1, 'Lebih tinggi lebih baik');
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 61.47],
            ['year' => 2020, 'value' => 55.72],
            ['year' => 2021, 'value' => 57.95],
            ['year' => 2022, 'value' => 63.22],
            ['year' => 2023, 'value' => 67.76],
        ]);
        $r = $this->addRow($indEko, 'Laju Pertumbuhan Ekonomi (y-on-y)', 'yearly', 'timeseries', '%', 2);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 5.77],
            ['year' => 2020, 'value' => -2.29],
            ['year' => 2021, 'value' => 3.12],
            ['year' => 2022, 'value' => 5.16],
            ['year' => 2023, 'value' => 5.01],
        ]);

        // (5) Ketimpangan & Distribusi
        $indKetimp = $this->addIndicator($idTegal, 'Ketimpangan & Distribusi', 'KETIMP');
        $r = $this->addRow($indKetimp, 'Gini Ratio', 'yearly', 'timeseries', null, 1, 'Lebih rendah lebih baik');
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2021, 'value' => 0.38],
            ['year' => 2022, 'value' => 0.37],
            ['year' => 2023, 'value' => 0.38],
        ]);
        $r = $this->addRow($indKetimp, 'Distribusi Pendapatan (Proporsi)', 'yearly', 'proporsi', '%', 2);
        $this->addProporsi($r, $idTegal, 2023, null, null, [
            '40% bawah'  => 18.43,
            '40% tengah' => 35.56,
            '20% atas'   => 46.01,
        ]);

        // (6) Dimensi IPM
        $indIPM = $this->addIndicator($idTegal, 'Dimensi IPM', 'IPM');
        $r = $this->addRow($indIPM, 'Umur Harapan Hidup', 'yearly', 'timeseries', 'tahun', 1);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 74.34],
            ['year' => 2020, 'value' => 74.46],
            ['year' => 2021, 'value' => 74.54],
            ['year' => 2022, 'value' => 74.64],
            ['year' => 2023, 'value' => 74.84],
            ['year' => 2024, 'value' => 75.01],
        ]);
        $r = $this->addRow($indIPM, 'Harapan Lama Sekolah', 'yearly', 'timeseries', 'tahun', 2);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 13.04],
            ['year' => 2020, 'value' => 13.05],
            ['year' => 2021, 'value' => 13.07],
            ['year' => 2022, 'value' => 13.08],
            ['year' => 2023, 'value' => 13.18],
            ['year' => 2024, 'value' => 13.25],
        ]);
        $r = $this->addRow($indIPM, 'Rata-rata Lama Sekolah (RLS)', 'yearly', 'timeseries', 'tahun', 3);
        $this->addValuesSingle($r, $idTegal, [
            ['year' => 2019, 'value' => 8.31],
            ['year' => 2020, 'value' => 8.51],
            ['year' => 2021, 'value' => 8.73],
            ['year' => 2022, 'value' => 9.00],
            ['year' => 2023, 'value' => 9.24],
            ['year' => 2024, 'value' => 9.28],
        ]);

        // BAR (jumlah_kategori) tahunan single-var
        $indYBar = $this->addIndicator($idTegal, 'UMKM', 'D_Y_BAR_KOTA');
        $rowYBar = $this->addRow($indYBar, 'Jumlah UMKM Terdaftar', 'yearly', 'jumlah_kategori', 'unit', 1);
        $varYBar = $this->addSingleVar($rowYBar, 'Jumlah');
        $this->addValuesSingleVar($rowYBar, $idTegal, $varYBar, [
            ['year' => 2019, 'value' => 5400],
            ['year' => 2020, 'value' => 5600],
            ['year' => 2021, 'value' => 5900],
            ['year' => 2022, 'value' => 6200],
            ['year' => 2023, 'value' => 6500],
            ['year' => 2024, 'value' => 7000],
        ]);

        /* =====================================================
         * ===============  K A B U P A T E N  =================
         * ===================================================== */

        $indKab = $this->addIndicator($idKab, 'PDRB Atas Dasar Harga Berlaku', 'PDRB_KAB');
        $rowKab = $this->addRow($indKab, 'Nilai PDRB', 'yearly', 'jumlah_kategori', 'Miliar Rupiah', 1);
        $varKab = $this->addSingleVar($rowKab, 'Jumlah');
        $this->addValuesSingleVar($rowKab, $idKab, $varKab, [
            ['year' => 2019, 'value' => 14500],
            ['year' => 2020, 'value' => 15050],
            ['year' => 2021, 'value' => 16010],
            ['year' => 2022, 'value' => 17500],
            ['year' => 2023, 'value' => 18900],
            ['year' => 2024, 'value' => 20100],
        ]);

        /* =====================================================
 * ===============  K O T A   D U M M Y  ===============
 * ===================================================== */

        // LINE Tahunan (2019–2024)
        $indDY = $this->addIndicator($idDummy, 'Contoh Timeseries Tahunan', 'D_Y_LINE');
        $rowDY = $this->addRow($indDY, 'Produksi Beras', 'yearly', 'timeseries', 'ton', 1);
        $this->addValuesSingle($rowDY, $idDummy, [
            ['year' => 2019, 'value' => 12000],
            ['year' => 2020, 'value' => 11800],
            ['year' => 2021, 'value' => 12600],
            ['year' => 2022, 'value' => 13050],
            ['year' => 2023, 'value' => 12870],
            ['year' => 2024, 'value' => 13540],
        ]);

        // LINE Triwulanan (2023 & 2024 lengkap)
        $indDQ = $this->addIndicator($idDummy, 'Contoh Timeseries Triwulanan', 'D_Q_LINE');
        $rowDQ = $this->addRow($indDQ, 'Produksi Ikan (Triwulanan)', 'quarterly', 'timeseries', 'ton', 1);
        $this->addValuesQuarterly($rowDQ, $idDummy, 2023, [1 => 320, 2 => 410, 3 => 380, 4 => 450]);
        $this->addValuesQuarterly($rowDQ, $idDummy, 2024, [1 => 360, 2 => 430, 3 => 405, 4 => 470]);

        // LINE Bulanan (2024 Jan–Des)
        $indDM = $this->addIndicator($idDummy, 'Contoh Timeseries Bulanan', 'D_M_LINE');
        $rowDM = $this->addRow($indDM, 'Kunjungan Wisatawan (Bulanan)', 'monthly', 'timeseries', 'orang', 1);
        $this->addValuesMonthly($rowDM, $idDummy, 2024, [
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

        // BAR Tahunan Single-Var (2019–2024)
        $indYB = $this->addIndicator($idDummy, 'Contoh Bar Tahunan', 'D_Y_BAR');
        $rowYB = $this->addRow($indYB, 'Jumlah UMKM Terdaftar', 'yearly', 'jumlah_kategori', 'unit', 1);
        $varYB = $this->addSingleVar($rowYB, 'Jumlah');
        $this->addValuesSingleVar($rowYB, $idDummy, $varYB, [
            ['year' => 2019, 'value' => 5400],
            ['year' => 2020, 'value' => 5600],
            ['year' => 2021, 'value' => 5900],
            ['year' => 2022, 'value' => 6200],
            ['year' => 2023, 'value' => 6500],
            ['year' => 2024, 'value' => 7000],
        ]);

        // BAR Triwulanan Single-Var (2023 & 2024 lengkap)
        $indQB = $this->addIndicator($idDummy, 'Contoh Bar Triwulanan', 'D_Q_BAR');
        $rowQB = $this->addRow($indQB, 'Produksi Hortikultura (Triwulanan)', 'quarterly', 'jumlah_kategori', 'ton', 1);
        $varQB = $this->addSingleVar($rowQB, 'Jumlah');
        $this->addValuesQuarterlyVar($rowQB, $idDummy, $varQB, 2023, [1 => 820, 2 => 900, 3 => 880, 4 => 950]);
        $this->addValuesQuarterlyVar($rowQB, $idDummy, $varQB, 2024, [1 => 860, 2 => 940, 3 => 905, 4 => 980]);

        // BAR Bulanan Single-Var (2024 Jan–Des)
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

        // BAR Tahunan Multi-Variabel (2019–2024)
        $indYBM = $this->addIndicator($idDummy, 'Contoh Bar Tahunan Multi Variabel', 'D_Y_BAR_MULTI');
        $rowYBM = $this->addRow($indYBM, 'Produksi Komoditas Pertanian', 'yearly', 'jumlah_kategori', 'ton', 1);
        $varIds = $this->addVars($rowYBM, ['Padi', 'Jagung', 'Kedelai']);
        $this->addValuesSingleVar($rowYBM, $idDummy, $varIds[0], [
            ['year' => 2019, 'value' => 1100],
            ['year' => 2020, 'value' => 1080],
            ['year' => 2021, 'value' => 1150],
            ['year' => 2022, 'value' => 1180],
            ['year' => 2023, 'value' => 1205],
            ['year' => 2024, 'value' => 1230],
        ]);
        $this->addValuesSingleVar($rowYBM, $idDummy, $varIds[1], [
            ['year' => 2019, 'value' => 900],
            ['year' => 2020, 'value' => 920],
            ['year' => 2021, 'value' => 950],
            ['year' => 2022, 'value' => 990],
            ['year' => 2023, 'value' => 1010],
            ['year' => 2024, 'value' => 1040],
        ]);
        $this->addValuesSingleVar($rowYBM, $idDummy, $varIds[2], [
            ['year' => 2019, 'value' => 300],
            ['year' => 2020, 'value' => 310],
            ['year' => 2021, 'value' => 320],
            ['year' => 2022, 'value' => 330],
            ['year' => 2023, 'value' => 340],
            ['year' => 2024, 'value' => 350],
        ]);

        // PIE Tahunan (2024)
        $indYP = $this->addIndicator($idDummy, 'Contoh Proporsi Tahunan', 'D_Y_PIE');
        $rowYP = $this->addRow($indYP, 'Pangsa Sektor Ekonomi', 'yearly', 'proporsi', '%', 1);
        $this->addProporsi($rowYP, $idDummy, 2024, null, null, [
            'Pertanian' => 18.5,
            'Industri' => 36.2,
            'Jasa' => 45.3
        ]);

        // PIE Triwulanan (2024 Q1–Q4)
        $indQP = $this->addIndicator($idDummy, 'Contoh Proporsi Triwulanan', 'D_Q_PIE');
        $rowQP = $this->addRow($indQP, 'Struktur Pengeluaran RT', 'quarterly', 'proporsi', '%', 1);
        $this->addProporsi($rowQP, $idDummy, 2024, 1, null, ['Pangan' => 52.0, 'Non-Pangan' => 48.0]);
        $this->addProporsi($rowQP, $idDummy, 2024, 2, null, ['Pangan' => 51.0, 'Non-Pangan' => 49.0]);
        $this->addProporsi($rowQP, $idDummy, 2024, 3, null, ['Pangan' => 50.0, 'Non-Pangan' => 50.0]);
        $this->addProporsi($rowQP, $idDummy, 2024, 4, null, ['Pangan' => 49.5, 'Non-Pangan' => 50.5]);

        // PIE Bulanan (2024 Jan–Des, total 100%)
        $indMP = $this->addIndicator($idDummy, 'Contoh Proporsi Bulanan', 'D_M_PIE');
        $rowMP = $this->addRow($indMP, 'Moda Transportasi', 'monthly', 'proporsi', '%', 1);
        $moda = [
            1 => ['Darat' => 70, 'Laut' => 20, 'Udara' => 10],
            2 => ['Darat' => 69, 'Laut' => 20, 'Udara' => 11],
            3 => ['Darat' => 68, 'Laut' => 21, 'Udara' => 11],
            4 => ['Darat' => 68, 'Laut' => 21, 'Udara' => 11],
            5 => ['Darat' => 67, 'Laut' => 21, 'Udara' => 12],
            6 => ['Darat' => 66, 'Laut' => 22, 'Udara' => 12],
            7 => ['Darat' => 66, 'Laut' => 22, 'Udara' => 12],
            8 => ['Darat' => 65, 'Laut' => 22, 'Udara' => 13],
            9 => ['Darat' => 66, 'Laut' => 22, 'Udara' => 12],
            10 => ['Darat' => 66, 'Laut' => 22, 'Udara' => 12],
            11 => ['Darat' => 65, 'Laut' => 22, 'Udara' => 13],
            12 => ['Darat' => 65, 'Laut' => 22, 'Udara' => 13],
        ];
        foreach ($moda as $m => $vals) {
            $this->addProporsi($rowMP, $idDummy, 2024, null, $m, $vals);
        }

        // LINE Triwulanan 2 (2023 & 2024)
        $indQL2 = $this->addIndicator($idDummy, 'Contoh Line Triwulanan 2', 'D_Q_LINE_2');
        $rowQL2 = $this->addRow($indQL2, 'Indeks Aktivitas Logistik (Triwulanan)', 'quarterly', 'timeseries', 'indeks', 1);
        $this->addValuesQuarterly($rowQL2, $idDummy, 2023, [1 => 99.2, 2 => 101.5, 3 => 100.1, 4 => 102.3]);
        $this->addValuesQuarterly($rowQL2, $idDummy, 2024, [1 => 100.7, 2 => 103.0, 3 => 101.2, 4 => 104.1]);

        // LINE Bulanan 2 (2024 Jan–Des)
        $indML2 = $this->addIndicator($idDummy, 'Contoh Line Bulanan 2', 'D_M_LINE_2');
        $rowML2 = $this->addRow($indML2, 'Indeks Harga Grosir (Bulanan)', 'monthly', 'timeseries', 'indeks', 1);
        $this->addValuesMonthly($rowML2, $idDummy, 2024, [
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

        // PIE Triwulanan 2 (2024 Q1–Q4, 3 kategori)
        $indQP2 = $this->addIndicator($idDummy, 'Contoh Proporsi Triwulanan 2', 'D_Q_PIE_2');
        $rowQP2 = $this->addRow($indQP2, 'Pangsa Kunjungan Objek Wisata', 'quarterly', 'proporsi', '%', 1);
        $this->addProporsi($rowQP2, $idDummy, 2024, 1, null, ['Pantai' => 42.0, 'Kota' => 34.0, 'Pegunungan' => 24.0]);
        $this->addProporsi($rowQP2, $idDummy, 2024, 2, null, ['Pantai' => 44.0, 'Kota' => 33.0, 'Pegunungan' => 23.0]);
        $this->addProporsi($rowQP2, $idDummy, 2024, 3, null, ['Pantai' => 40.0, 'Kota' => 35.0, 'Pegunungan' => 25.0]);
        $this->addProporsi($rowQP2, $idDummy, 2024, 4, null, ['Pantai' => 41.0, 'Kota' => 34.0, 'Pegunungan' => 25.0]);

        // PIE Bulanan 2 (2024 Jan–Des, 4 kategori; total 100%)
        $indMP2 = $this->addIndicator($idDummy, 'Contoh Proporsi Bulanan 2', 'D_M_PIE_2');
        $rowMP2 = $this->addRow($indMP2, 'Moda Perjalanan Bulanan', 'monthly', 'proporsi', '%', 1);
        $moda2 = [
            1 => ['Darat' => 61, 'Laut' => 15, 'Udara' => 20, 'Lainnya' => 4],
            2 => ['Darat' => 61, 'Laut' => 15, 'Udara' => 20, 'Lainnya' => 4],
            3 => ['Darat' => 62, 'Laut' => 14, 'Udara' => 20, 'Lainnya' => 4],
            4 => ['Darat' => 61, 'Laut' => 15, 'Udara' => 20, 'Lainnya' => 4],
            5 => ['Darat' => 60, 'Laut' => 16, 'Udara' => 20, 'Lainnya' => 4],
            6 => ['Darat' => 59, 'Laut' => 17, 'Udara' => 21, 'Lainnya' => 3],
            7 => ['Darat' => 59, 'Laut' => 17, 'Udara' => 21, 'Lainnya' => 3],
            8 => ['Darat' => 58, 'Laut' => 18, 'Udara' => 21, 'Lainnya' => 3],
            9 => ['Darat' => 59, 'Laut' => 17, 'Udara' => 21, 'Lainnya' => 3],
            10 => ['Darat' => 59, 'Laut' => 17, 'Udara' => 21, 'Lainnya' => 3],
            11 => ['Darat' => 58, 'Laut' => 18, 'Udara' => 21, 'Lainnya' => 3],
            12 => ['Darat' => 58, 'Laut' => 18, 'Udara' => 21, 'Lainnya' => 3],
        ];
        foreach ($moda2 as $m => $vals) {
            $this->addProporsi($rowMP2, $idDummy, 2024, null, $m, $vals);
        }
    }
}
