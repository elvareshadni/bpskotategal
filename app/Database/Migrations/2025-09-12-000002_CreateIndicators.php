<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIndicators extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'region_id'  => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'name'       => ['type'=>'VARCHAR','constraint'=>150],
            'code'       => ['type'=>'VARCHAR','constraint'=>50,'null'=>true], // opsional utk API by ?key
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('region_id');
        $this->forge->addForeignKey('region_id','regions','id','CASCADE','CASCADE');
        $this->forge->createTable('indicators', true);
    }

    public function down()
    {
        $this->forge->dropTable('indicators', true);
    }
}
