<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JobCard extends Model
{
    protected $fillable = [
        'order_no','reference_no','invoice_no','invoice_date','customer_id','customer_name','customer_address','customer_email',
        'customer_nic','customer_dob','phone_no','device_name','device_brand','serial_no',
        'device_age','device_fault','device_photo','item_description','issue','date','rupees','advance_amount','discount','paid_amount','payment_status','status','priority',
        'estimated_delivery','accessories','remark','need_assistant','employee_id','payment_received',
        'cancelled_reason','cancelled_at','reminder_sent_count','last_reminder_sent_at',
    ];

    protected $appends = ['grand_total', 'balance', 'subtotal'];

    protected $casts = [
        'need_assistant'  => 'boolean',
        'payment_received'=> 'boolean',
        'date'            => 'date',
        'estimated_delivery' => 'date',
        'invoice_date'    => 'date',
        'cancelled_at'           => 'datetime',
        'last_reminder_sent_at'  => 'datetime',
    ];

    public function employee()    { return $this->belongsTo(Employee::class); }
    public function invoiceItems(){ return $this->hasMany(InvoiceItem::class); }
    public function paymentLogs() { return $this->hasMany(PaymentLog::class)->orderBy('paid_at'); }

    public static function nextOrderNo(): string
    {
        $prefix    = date('ym');
        $prefixLen = strlen($prefix);
        $last = static::where('order_no', 'like', $prefix . '%')
                       ->orderByDesc('id')->value('order_no');
        $serial = $last ? intval(substr($last, $prefixLen)) + 1 : 1;
        return $prefix . str_pad($serial, 3, '0', STR_PAD_LEFT);
    }

    public static function nextReferenceNo(): string
    {
        $yymm   = now()->format('ym');
        $shopId = session('shop_id');
        $query  = $shopId ? static::where('shop_id', $shopId) : static::query();
        $last   = $query->where('reference_no', 'like', "REF-{$yymm}%")->max('reference_no');
        $seq    = $last ? ((int)substr($last, -3) + 1) : 1;
        return 'REF-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public static function nextCustomerId(): string
    {
        $shopId = session('shop_id');
        $query  = $shopId ? static::where('shop_id', $shopId) : static::query();
        $last   = $query->orderByDesc('id')->value('customer_id');
        if (!$last) return 'CUS-001';
        preg_match('/(\d+)$/', $last, $m);
        $next = isset($m[1]) ? intval($m[1]) + 1 : 1;
        return 'CUS-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public static function nextInvoiceNo(): string
    {
        $prefix = 'INV-' . date('Y') . '-';
        $last = static::where('invoice_no', 'like', $prefix . '%')
                       ->orderByDesc('id')->value('invoice_no');
        $serial = $last ? intval(substr($last, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($serial, 4, '0', STR_PAD_LEFT);
    }

    // Computed helpers
    // subtotal = service charge (rupees) + all line items
    public function getSubtotalAttribute(): float
    {
        $itemsTotal = (float)$this->invoiceItems->sum('total');
        return (float)$this->rupees + $itemsTotal;
    }

    public function getGrandTotalAttribute(): float
    {
        return max(0, $this->subtotal - (float)$this->discount);
    }

    public function getBalanceAttribute(): float
    {
        return max(0, $this->grand_total - (float)$this->paid_amount);
    }
}
