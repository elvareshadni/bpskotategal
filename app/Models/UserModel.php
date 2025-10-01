<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'fullname',
        'phone',
        'photo',
        'role',

        // kolom baru untuk Google Sign-In
        'google_id',
        'auth_provider',
        'email_verified_at',
    ];
}
