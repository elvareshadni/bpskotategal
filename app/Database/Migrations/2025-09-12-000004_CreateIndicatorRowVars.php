<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIndicatorRowVars extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'row_id'     => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'name'       => ['type'=>'VARCHAR','constraint'=>150],
            'sort_order' => ['type'=>'INT','constraint'=>11,'default'=>0],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('row_id');
        $this->forge->addForeignKey('row_id','indicator_rows','id','CASCADE','CASCADE');
        $this->forge->createTable('indicator_row_vars', true);
    }

    public function down()
    {
        $this->forge->dropTable('indicator_row_vars', true);
    }
}
