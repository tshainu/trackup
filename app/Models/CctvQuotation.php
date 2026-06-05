<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvQuotation extends Model
{
    use ShopScoped;
    protected $table = 'cctv_quotations';
    protected $fillable = [
        'shop_id', 'quote_no', 'lead_id', 'customer_id', 'customer_name', 'mobile',
        'equipment_list', 'labour_cost', 'installation_cost', 'transport_cost',
        'discount', 'tax', 'grand_total', 'status', 'valid_until', 'notes',
    ];
    protected $casts = [
        'equipment_list'   => 'array',
        'labour_cost'      => 'float',
        'installation_cost'=> 'float',
        'transport_cost'   => 'float',
        'discount'         => 'float',
        'tax'              => 'float',
        'grand_total'      => 'float',
        'valid_until'      => 'date',
    ];

    public static function nextQuoteNo(): string
    {
        $yymm = now()->format('ym');
        $shopId = session('shop_id');
        $last = static::withoutGlobalScope('shop')->where('shop_id', $shopId)
                      ->where('quote_no', 'like', "QUO-{$yymm}%")->max('quote_no');
        $seq = $last ? ((int)substr($last, -3) + 1) : 1;
        return 'QUO-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function computeTotal(): float
    {
        $items = collect($this->equipment_list ?? []);
        $equipTotal = $items->sum(fn($i) => ($i['qty'] ?? 0) * ($i['unit_price'] ?? 0));
        $subtotal = $equipTotal + $this->labour_cost + $this->installation_cost + $this->transport_cost;
        return max(0, $subtotal - $this->discount + $this->tax);
    }

    // ── Accessors: alias view field names → real DB columns ──────────
    public function getQuotationNoAttribute()      { return $this->quote_no; }
    public function getTotalAmountAttribute()      { return $this->grand_total; }
    public function getDiscountAmountAttribute()   { return $this->discount; }
    public function getInstallationChargeAttribute(){ return $this->installation_cost; }
    public function getSubTotalAttribute() {
        $items = collect($this->equipment_list ?? []);
        return $items->sum(fn($i) => ($i['qty'] ?? 0) * ($i['unit_price'] ?? 0))
             + ($this->labour_cost ?? 0)
             + ($this->installation_cost ?? 0)
             + ($this->transport_cost ?? 0);
    }

    public function lead()     { return $this->belongsTo(CctvLead::class, 'lead_id'); }
    public function customer() { return $this->belongsTo(Customer::class); }
}
