<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePasswordResets extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'email'      => ['type'=>'VARCHAR','constraint'=>100,'null'=>false],
            'token_hash' => ['type'=>'VARCHAR','constraint'=>255,'null'=>false],
            'expires_at' => ['type'=>'DATETIME','null'=>false],
            'used_at'    => ['type'=>'DATETIME','null'=>true],
            'created_at' => ['type'=>'TIMESTAMP','default'=>new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email');       // index email
        $this->forge->addKey('expires_at');  // index expires_at
        $this->forge->createTable('password_resets');
    }

    public function down()
    {
        $this->forge->dropTable('password_resets');
    }
}
