<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvInventory extends Model
{
    use ShopScoped;
    protected $table = 'cctv_inventory';
    protected $fillable = [
        'shop_id', 'item_code', 'name', 'category', 'brand', 'model',
        'qty_in_stock', 'low_stock_alert', 'unit_cost', 'selling_price', 'notes',
    ];
    protected $casts = [
        'unit_cost'     => 'float',
        'selling_price' => 'float',
    ];

    public function logs() { return $this->hasMany(CctvInventoryLog::class, 'inventory_id'); }

    public function isLowStock(): bool
    {
        return $this->qty_in_stock <= $this->low_stock_alert;
    }
}
