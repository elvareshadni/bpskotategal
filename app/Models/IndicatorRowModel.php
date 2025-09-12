<?php

namespace App\Models;

use CodeIgniter\Model;

class IndicatorRowModel extends Model
{
    protected $table      = 'indicator_rows';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['indicator_id','subindikator','timeline','data_type','unit','sort_order'];
    protected $useTimestamps = true;
}
