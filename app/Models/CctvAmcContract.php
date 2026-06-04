<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvAmcContract extends Model
{
    use ShopScoped;
    protected $table = 'cctv_amc_contracts';
    protected $fillable = [
        'shop_id', 'amc_no', 'customer_id', 'customer_name', 'mobile', 'address',
        'project_id', 'start_date', 'end_date', 'contract_value', 'visit_frequency',
        'visits_included', 'visits_used', 'status', 'notes',
    ];
    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'contract_value' => 'float',
    ];

    public static function nextAmcNo(): string
    {
        $yymm = now()->format('ym');
        $shopId = session('shop_id');
        $last = static::withoutGlobalScope('shop')->where('shop_id', $shopId)
                      ->where('amc_no', 'like', "AMC-{$yymm}%")->max('amc_no');
        $seq = $last ? ((int)substr($last, -3) + 1) : 1;
        return 'AMC-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function customer()  { return $this->belongsTo(Customer::class); }
    public function project()   { return $this->belongsTo(CctvProject::class, 'project_id'); }
    public function visits()    { return $this->hasMany(CctvAmcVisit::class, 'amc_id'); }
    public function tickets()   { return $this->hasMany(CctvServiceTicket::class, 'amc_id'); }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function daysToExpiry(): int
    {
        return $this->end_date ? (int) now()->diffInDays($this->end_date, false) : 0;
    }
}
