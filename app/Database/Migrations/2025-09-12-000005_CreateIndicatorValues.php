<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIndicatorValues extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type'=>'BIGINT','unsigned'=>true,'auto_increment'=>true],
            'row_id'    => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'region_id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'var_id'    => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true], // null utk single
            'year'      => ['type'=>'INT','constraint'=>4,'null'=>true],
            'quarter'   => ['type'=>'TINYINT','constraint'=>1,'null'=>true],
            'month'     => ['type'=>'TINYINT','constraint'=>2,'null'=>true],
            'value'     => ['type'=>'DOUBLE','null'=>true],
            'created_at'=> ['type'=>'DATETIME','null'=>true],
            'updated_at'=> ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['row_id','region_id']);
        $this->forge->addForeignKey('row_id','indicator_rows','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('region_id','regions','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('var_id','indicator_row_vars','id','CASCADE','CASCADE');
        $this->forge->createTable('indicator_values', true);
    }

    public function down()
    {
        $this->forge->dropTable('indicator_values', true);
    }
}
