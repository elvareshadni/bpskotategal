<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIndicatorRows extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'indicator_id'  => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'subindikator'  => ['type'=>'VARCHAR','constraint'=>200],
            'timeline'      => ['type'=>'ENUM','constraint'=>['yearly','quarterly','monthly'],'default'=>'yearly'],
            // UBAH: bentuk data sekarang 3 jenis
            'data_type'     => ['type'=>'ENUM','constraint'=>['timeseries','jumlah_kategori','proporsi'],'default'=>'timeseries'],
            'unit'          => ['type'=>'VARCHAR','constraint'=>50,'null'=>true],
            // TAMBAH: interpretasi opsional
            'interpretasi'  => ['type'=>'TEXT','null'=>true],
            'sort_order'    => ['type'=>'INT','constraint'=>11,'default'=>0],
            'created_at'    => ['type'=>'DATETIME','null'=>true],
            'updated_at'    => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('indicator_id');
        $this->forge->addForeignKey('indicator_id','indicators','id','CASCADE','CASCADE');
        $this->forge->createTable('indicator_rows', true);
    }


    public function down()
    {
        $this->forge->dropTable('indicator_rows', true);
    }
}
