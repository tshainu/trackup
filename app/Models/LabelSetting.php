<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabelSetting extends Model
{
    protected $fillable = ['shop_id', 'width_mm', 'height_mm', 'font_size'];

    public static function current(): self
    {
        $shopId = session('shop_id');
        return self::firstOrCreate(
            ['shop_id' => $shopId],
            ['width_mm' => 62, 'height_mm' => 29, 'font_size' => 10]
        );
    }
}
