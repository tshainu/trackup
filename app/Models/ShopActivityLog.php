<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopActivityLog extends Model
{
    protected $fillable = ['shop_id', 'action', 'description', 'performed_by'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function performer()
    {
        return $this->belongsTo(SuperAdmin::class, 'performed_by');
    }
}
