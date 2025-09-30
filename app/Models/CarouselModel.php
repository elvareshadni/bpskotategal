<?php
namespace App\Models;

use CodeIgniter\Model;

class CarouselModel extends Model
{
    protected $table      = 'carousel';
    protected $primaryKey = 'id';

    protected $allowedFields = ['judul', 'gambar', 'posisi', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
