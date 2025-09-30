<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CarouselSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            [
                'judul'      => 'Pencatatan Perdana Saham',
                'gambar'     => 'hero1.jpg',
                'posisi'     => 'center',
                'link_url'   => 'https://www.idx.co.id/id',
                'created_at' => $now, 'updated_at' => null,
            ],
            [
                'judul'      => 'Laporan Keuangan',
                'gambar'     => 'hero2.png',
                'posisi'     => 'start',
                'link_url'   => 'https://www.idx.co.id/id/berita/laporan-keuangan',
                'created_at' => $now, 'updated_at' => null,
            ],
            [
                'judul'      => 'E-IPO Informasi',
                'gambar'     => 'hero3.jpg',
                'posisi'     => 'end',
                'link_url'   => 'https://e-ipo.co.id/',
                'created_at' => $now, 'updated_at' => null,
            ],
        ];

        $this->db->table('carousel')->insertBatch($data);
    }
}
