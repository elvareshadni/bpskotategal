<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateCarousel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'judul'      => ['type'=>'VARCHAR','constraint'=>255,'null'=>false],
            'gambar'     => ['type'=>'VARCHAR','constraint'=>255,'null'=>false],
            'posisi'     => ['type'=>'ENUM','constraint'=>['start','center','end'],'default'=>'center'],
            'created_at' => ['type'=>'TIMESTAMP','default'=>new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('carousel');
    }

    public function down()
    {
        $this->forge->dropTable('carousel');
    }
}
