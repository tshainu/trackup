<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvInventoryLog extends Model
{
    use ShopScoped;
    protected $table = 'cctv_inventory_logs';
    protected $fillable = ['shop_id', 'inventory_id', 'type', 'qty', 'reference', 'note', 'unit_cost'];
    protected $casts = ['unit_cost' => 'float'];

    public function item() { return $this->belongsTo(CctvInventory::class, 'inventory_id'); }
}
