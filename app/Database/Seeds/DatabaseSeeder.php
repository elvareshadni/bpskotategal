<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(UsersSeeder::class);
        $this->call(InfografisSeeder::class);
        $this->call(CarouselSeeder::class);
        $this->call(RegionsSeeder::class);
        $this->call(IndicatorsSeeder::class);
    }
}
