<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'shop_name','shop_code','owner_name','email','phone','address',
        'city','country','logo','admin_username','admin_password_hash',
        'admin_plain_password','status','last_active_at','notes','modules',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'modules'        => 'array',
    ];

    public function activityLogs()
    {
        return $this->hasMany(ShopActivityLog::class);
    }

    public function isOnline(): bool
    {
        return $this->last_active_at && $this->last_active_at->diffInMinutes(now()) <= 15;
    }
}
