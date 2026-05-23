<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $fillable = ['user_name','email','password','code','status'];
    protected $hidden = ['password'];
}
