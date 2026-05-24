<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $fillable = ['user_id','employee_name','registration_no','employee_address','nic','phone_no_1','phone_no_2','email','user_name','role','type','password','code','status','photo'];
    protected $hidden = ['password'];

    public function jobCards()
    {
        return $this->hasMany(JobCard::class);
    }
}
