<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CarouselSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'judul'  => 'Hero 1',
                'gambar' => 'hero1.jpg',
                'posisi' => 'center',
            ],
            [
                'judul'  => 'Hero 2',
                'gambar' => 'hero2.png',
                'posisi' => 'start',
            ],
        ];

        $this->db->table('carousel')->insertBatch($data);
    }
}
