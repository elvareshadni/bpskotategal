<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegionsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['code_bps'=>'3372','name'=>'Kota Tegal','is_default'=>1],
            ['code_bps'=>'3328','name'=>'Kabupaten Tegal','is_default'=>0],
        ];
        $this->db->table('regions')->insertBatch($data);
    }
}
