<?php

namespace App\Models;

use CodeIgniter\Model;

class RegionModel extends Model
{
    protected $table      = 'regions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code_bps','name','is_default'];
    protected $useTimestamps = true;
}
