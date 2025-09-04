<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InfografisSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'judul'     => 'IPM 2020',
                'deskripsi' => 'Indeks Pembangunan Manusia 2020',
                'gambar'    => 'cover.jpg',
                'tanggal'   => '2025-08-29',
            ],
            [
                'judul'     => 'Sensus Penduduk 2020',
                'deskripsi' => 'Jumlah Penduduk Jawa Barat Hasil SP 2020',
                'gambar'    => 'cover2.jpg', // atau pakai nama file hasil uploadmu
                'tanggal'   => '2025-08-29',
            ],
        ];

        $this->db->table('infografis')->insertBatch($data);
    }
}
