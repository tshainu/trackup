<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappSetting extends Model
{
    protected $fillable = ['api_url', 'api_key', 'instance_id', 'phone_number_id', 'enabled'];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return self::first() ?? self::create([
            'api_url'         => '',
            'api_key'         => '',
            'instance_id'     => '',
            'phone_number_id' => '',
            'enabled'         => false,
        ]);
    }
}
