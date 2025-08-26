<?php

namespace App\Models;

use CodeIgniter\Model;

class InfografisModel extends Model
{
    protected $table = 'infografis';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['title', 'slug', 'infografis_image'];

    public function getInfografis($slug = false)
    {
        if ($slug === false) {
            return $this->findAll();
        }
        return $this->where(['slug' => $slug])->first();
    }
}
