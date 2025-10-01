<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'username'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'fullname'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => true],
            'photo'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],

            // --- penting: password boleh NULL agar akun Google tidak punya password
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],

            // --- penanda akun Google & provider
            'google_id'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'auth_provider'    => ['type' => 'ENUM', 'constraint' => ['local','google'], 'default' => 'local'],
            'email_verified_at'=> ['type' => 'DATETIME', 'null' => true],

            'role'       => ['type' => 'ENUM', 'constraint' => ['admin', 'user'], 'default' => 'user'],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);

        $this->forge->addKey('id', true);               // PK
        $this->forge->addUniqueKey('email');            // unique email
        $this->forge->addUniqueKey('google_id');        // unique google id (NULL diperbolehkan)
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
