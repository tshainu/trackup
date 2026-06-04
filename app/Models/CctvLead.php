<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvLead extends Model
{
    use ShopScoped;
    protected $table = 'cctv_leads';
    protected $fillable = [
        'shop_id', 'lead_no', 'customer_id', 'customer_name', 'mobile',
        'address', 'customer_type', 'inquiry_date', 'inquiry_source',
        'requirement_notes', 'status',
    ];

    public static function nextLeadNo(): string
    {
        $yymm   = now()->format('ym');
        $shopId = session('shop_id');
        $last   = static::withoutGlobalScope('shop')->where('shop_id', $shopId)
                        ->where('lead_no', 'like', "LED-{$yymm}%")->max('lead_no');
        $seq = $last ? ((int)substr($last, -3) + 1) : 1;
        return 'LED-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function customer() { return $this->belongsTo(Customer::class); }
    public function surveys()  { return $this->hasMany(CctvSurvey::class, 'lead_id'); }
    public function quotations() { return $this->hasMany(CctvQuotation::class, 'lead_id'); }
}
