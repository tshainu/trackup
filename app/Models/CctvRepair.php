<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvRepair extends Model
{
    use ShopScoped;
    protected $table = 'cctv_repairs';
    protected $fillable = [
        'shop_id', 'repair_no', 'customer_id', 'customer_name', 'mobile',
        'device_type', 'brand', 'model', 'serial_number', 'fault_description',
        'technician_id', 'repair_notes', 'parts_used', 'repair_cost', 'paid_amount',
        'received_date', 'completed_date', 'status', 'notes',
    ];
    protected $casts = [
        'parts_used'     => 'array',
        'repair_cost'    => 'float',
        'paid_amount'    => 'float',
        'received_date'  => 'date',
        'completed_date' => 'date',
    ];

    public static function nextRepairNo(): string
    {
        $yymm = now()->format('ym');
        $shopId = session('shop_id');
        $last = static::withoutGlobalScope('shop')->where('shop_id', $shopId)
                      ->where('repair_no', 'like', "REP-{$yymm}%")->max('repair_no');
        $seq = $last ? ((int)substr($last, -3) + 1) : 1;
        return 'REP-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function customer()   { return $this->belongsTo(Customer::class); }
    public function technician() { return $this->belongsTo(Employee::class, 'technician_id'); }
}
