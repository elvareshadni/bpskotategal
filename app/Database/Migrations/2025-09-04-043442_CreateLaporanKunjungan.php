<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLaporanKunjungan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'user_id'      => ['type'=>'INT','unsigned'=>true,'null'=>true],
            'username'     => ['type'=>'VARCHAR','constraint'=>100,'null'=>true],
            'login_time'   => ['type'=>'DATETIME','null'=>false],
            'logout_time'  => ['type'=>'DATETIME','null'=>true],
            'durasi_waktu' => ['type'=>'TIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('laporan_kunjungan');
    }

    public function down()
    {
        $this->forge->dropTable('laporan_kunjungan');
    }
}
