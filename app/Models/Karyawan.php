<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Auth;

class Karyawan extends Auth
{
    //
    protected $table = 'karyawan';

    protected $fillable = ['username', 'nama', 'role', 'password'];

    protected $hidden = ['password', 'remember_token'];
}
