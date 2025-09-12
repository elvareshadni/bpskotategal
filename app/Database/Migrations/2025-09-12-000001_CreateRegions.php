<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'code_bps'   => ['type'=>'VARCHAR','constraint'=>10,'null'=>true],
            'name'       => ['type'=>'VARCHAR','constraint'=>100],
            'is_default' => ['type'=>'TINYINT','constraint'=>1,'default'=>0],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code_bps');
        $this->forge->createTable('regions', true);
    }

    public function down()
    {
        $this->forge->dropTable('regions', true);
    }
}
