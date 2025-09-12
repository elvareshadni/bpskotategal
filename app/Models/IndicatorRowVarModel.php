<?php

namespace App\Models;

use CodeIgniter\Model;

class IndicatorRowVarModel extends Model
{
    protected $table      = 'indicator_row_vars';
    protected $primaryKey = 'id';
    protected $allowedFields = ['row_id','name','sort_order'];
    protected $useTimestamps = true;
}
