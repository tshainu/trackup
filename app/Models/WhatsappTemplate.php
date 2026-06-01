<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $fillable = ['key', 'label', 'message', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public static function forKey(string $key): ?self
    {
        return self::where('key', $key)->where('active', true)->first();
    }
}
