<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCarousel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT',     'unsigned' => true, 'auto_increment' => true],
            'judul'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'gambar'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'link_url'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('carousel', true);
    }

    public function down()
    {
        $this->forge->dropTable('carousel', true);
    }
}
