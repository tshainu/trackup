<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappSetting extends Model
{
    protected $fillable = [
        'shop_id', 'api_url', 'api_key', 'instance_id', 'phone_number_id', 'enabled',
        'uncollected_reminder_enabled', 'uncollected_reminder_count', 'uncollected_reminder_interval_hours',
    ];

    protected $casts = [
        'enabled'                     => 'boolean',
        'uncollected_reminder_enabled'=> 'boolean',
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
