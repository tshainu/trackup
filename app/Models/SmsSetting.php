<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    use ShopScoped;

    protected $fillable = ['shop_id','api_url','api_key','sender_id','enabled'];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public static function current(): self
    {
        $shopId = session('shop_id');
        return static::withoutGlobalScope('shop')
                     ->where('shop_id', $shopId)
                     ->first()
            ?? static::withoutGlobalScope('shop')->create([
                'shop_id'   => $shopId,
                'api_url'   => '',
                'api_key'   => '',
                'sender_id' => '',
                'enabled'   => false,
            ]);
    }
}
