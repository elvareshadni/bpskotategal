<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateInfografis extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'judul'      => ['type'=>'VARCHAR','constraint'=>255,'null'=>false],
            'deskripsi'  => ['type'=>'TEXT','null'=>true],
            'gambar'     => ['type'=>'VARCHAR','constraint'=>255,'null'=>false],
            'tanggal'    => ['type'=>'DATE','null'=>false],
            'created_at' => ['type'=>'TIMESTAMP','default'=>new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('infografis');
    }

    public function down()
    {
        $this->forge->dropTable('infografis');
    }
}
