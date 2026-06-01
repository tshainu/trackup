<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class SuperAdmin extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'status', 'last_login_at'];
    protected $hidden = ['password'];
    protected $casts = ['last_login_at' => 'datetime'];
}
