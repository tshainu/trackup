<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvServiceTicket extends Model
{
    use ShopScoped;
    protected $table = 'cctv_service_tickets';
    protected $fillable = [
        'shop_id', 'ticket_no', 'customer_id', 'customer_name', 'mobile', 'address',
        'ticket_type', 'complaint_details', 'priority', 'assigned_technician',
        'scheduled_date', 'service_charge', 'paid_amount', 'ticket_source', 'amc_id',
        'resolution_notes', 'signature_path', 'status', 'completed_at',
    ];
    protected $casts = [
        'scheduled_date' => 'date',
        'service_charge' => 'float',
        'paid_amount'    => 'float',
        'completed_at'   => 'datetime',
    ];

    public static function nextTicketNo(): string
    {
        $yymm = now()->format('ym');
        $shopId = session('shop_id');
        $last = static::withoutGlobalScope('shop')->where('shop_id', $shopId)
                      ->where('ticket_no', 'like', "CST-{$yymm}%")->max('ticket_no');
        $seq = $last ? ((int)substr($last, -3) + 1) : 1;
        return 'CST-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function customer()   { return $this->belongsTo(Customer::class); }
    public function technician() { return $this->belongsTo(Employee::class, 'assigned_technician'); }
    public function amc()        { return $this->belongsTo(CctvAmcContract::class, 'amc_id'); }
}
