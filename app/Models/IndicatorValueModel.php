<?php

namespace App\Models;

use CodeIgniter\Model;

class IndicatorValueModel extends Model
{
    protected $table      = 'indicator_values';
    protected $primaryKey = 'id';
    protected $allowedFields = ['row_id','region_id','var_id','year','quarter','month','value'];
    protected $useTimestamps = true;
}
