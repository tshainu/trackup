<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class DeliveredOrder extends Model
{
    use ShopScoped;

    protected $fillable = [
        'shop_id',
        'order_no','invoice_no','invoice_date','customer_id',
        'customer_name','customer_address','customer_email','customer_nic','customer_dob','phone_no',
        'device_name','device_brand','serial_no','device_age','device_fault','issue','item_description',
        'date','rupees','discount','paid_amount','grand_total','status','priority',
        'estimated_delivery','accessories','remark','need_assistant','employee_id','payment_received',
        'invoice_items','delivered_at',
    ];

    protected $casts = [
        'invoice_items'   => 'array',
        'need_assistant'  => 'boolean',
        'payment_received'=> 'boolean',
        'date'            => 'date',
        'estimated_delivery' => 'date',
        'invoice_date'    => 'date',
        'delivered_at'    => 'datetime',
    ];
}
