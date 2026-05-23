<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JobCard extends Model
{
    protected $fillable = [
        'order_no','customer_id','customer_name','customer_address','customer_email',
        'customer_nic','customer_dob','phone_no','device_name','device_brand','serial_no',
        'device_age','device_fault','issue','date','rupees','status','priority',
        'estimated_delivery','accessories','remark','need_assistant','employee_id','payment_received'
    ];

    protected $casts = ['need_assistant' => 'boolean', 'payment_received' => 'boolean', 'date' => 'date', 'estimated_delivery' => 'date'];

    public function employee() { return $this->belongsTo(Employee::class); }

    public static function nextOrderNo(): string
    {
        $prefix = date('ym'); // e.g. 2605 for May 2026
        $prefixLen = strlen($prefix); // 4 chars
        $last = static::where('order_no', 'like', $prefix . '%')
                       ->orderByDesc('id')
                       ->value('order_no');
        if (!$last) {
            $serial = 1;
        } else {
            // Extract only the serial portion after the 4-char prefix
            $serial = intval(substr($last, $prefixLen)) + 1;
        }
        return $prefix . str_pad($serial, 3, '0', STR_PAD_LEFT);
    }

    public static function nextCustomerId(): string
    {
        $last = static::orderByDesc('id')->value('customer_id');
        if (!$last) return 'CUS-001';
        preg_match('/(\d+)$/', $last, $m);
        $next = isset($m[1]) ? intval($m[1]) + 1 : 1;
        return 'CUS-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
