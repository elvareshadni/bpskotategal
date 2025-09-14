<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegionsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'code_bps'   => '3372',
                'name'       => 'Kota Tegal',
                'is_default' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code_bps'   => '3328',
                'name'       => 'Kabupaten Tegal',
                'is_default' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code_bps'   => '3373',
                'name'       => 'Kota Dummy',
                'is_default' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('regions')->insertBatch($data);
    }
}
