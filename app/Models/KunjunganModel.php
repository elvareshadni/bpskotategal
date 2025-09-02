<?php

namespace App\Models;

use CodeIgniter\Model;

class KunjunganModel extends Model
{
    protected $table      = 'kunjungan';   // nama tabel di database
    protected $primaryKey = 'id';          // kolom primary key
    protected $allowedFields = ['username', 'tanggal', 'durasi'];
}
