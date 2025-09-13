<?php

namespace App\Models;

use CodeIgniter\Model;

class IndicatorModel extends Model
{
    protected $table      = 'indicators';
    protected $primaryKey = 'id';
    protected $allowedFields = ['region_id','name'];
    protected $useTimestamps = true;
    
}
