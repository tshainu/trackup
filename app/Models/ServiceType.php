<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use ShopScoped;

    protected $fillable = ['shop_id','name','description','icon','base_charge','active','milestones'];

    protected $casts = [
        'base_charge' => 'float',
        'active'      => 'boolean',
        'milestones'  => 'array',
    ];
}
