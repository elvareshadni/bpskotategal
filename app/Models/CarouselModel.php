<?php

namespace App\Models;

use CodeIgniter\Model;

class CarouselModel extends Model
{
    protected $table      = 'carousel';
    protected $primaryKey = 'id';

    protected $allowedFields = ['judul', 'gambar', 'posisi'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; 
}
