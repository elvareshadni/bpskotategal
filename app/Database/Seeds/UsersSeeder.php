<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'fullname' => 'Admin',
                'email'    => 'admin@gmail.com',
                'phone'    => null,
                'photo'    => null,
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
            [
                'username' => 'elvares',
                'fullname' => 'elva',
                'email'    => 'elvareshadni@gmail.com',
                'phone'    => null,
                'photo'    => null,
                'password' => password_hash('user12345', PASSWORD_DEFAULT),
                'role'     => 'user',
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
