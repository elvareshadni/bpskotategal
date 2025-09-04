<?php

namespace App\Models;

use CodeIgniter\Model;

class KunjunganModel extends Model
{
    protected $table      = 'laporan_kunjungan';   // nama tabel di database
    protected $primaryKey = 'id';          // kolom primary key
    protected $allowedFields = ['user_id', 'username', 'login_time', 'logout_time', 'durasi_waktu', ];
}