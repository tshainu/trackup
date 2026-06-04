<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvAsset extends Model
{
    use ShopScoped;
    protected $table = 'cctv_assets';
    protected $fillable = [
        'shop_id', 'asset_id', 'project_id', 'customer_id', 'customer_name',
        'asset_type', 'serial_number', 'brand', 'model', 'installation_date',
        'warranty_expiry', 'location', 'status', 'notes',
    ];
    protected $casts = [
        'installation_date' => 'date',
        'warranty_expiry'   => 'date',
    ];

    public static function nextAssetId(): string
    {
        $yymm = now()->format('ym');
        $shopId = session('shop_id');
        $last = static::withoutGlobalScope('shop')->where('shop_id', $shopId)
                      ->where('asset_id', 'like', "AST-{$yymm}%")->max('asset_id');
        $seq = $last ? ((int)substr($last, -3) + 1) : 1;
        return 'AST-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function project()  { return $this->belongsTo(CctvProject::class, 'project_id'); }
    public function customer() { return $this->belongsTo(Customer::class); }

    public function isWarrantyActive(): bool
    {
        return $this->warranty_expiry && $this->warranty_expiry->isFuture();
    }
}
