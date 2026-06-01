<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

trait ShopScoped
{
    /**
     * Boot the trait - apply global scope to filter by shop_id.
     */
    protected static function bootShopScoped(): void
    {
        static::addGlobalScope('shop', function (Builder $builder) {
            $shopId = Session::get('shop_id');
            if ($shopId) {
                $builder->where(static::getModel()->getTable() . '.shop_id', $shopId);
            }
        });

        // Auto-inject shop_id on create
        static::creating(function ($model) {
            if (empty($model->shop_id)) {
                $model->shop_id = Session::get('shop_id');
            }
        });
    }
}
