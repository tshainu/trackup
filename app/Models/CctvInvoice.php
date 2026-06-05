<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvInvoice extends Model
{
    use ShopScoped;
    protected $table = 'cctv_invoices';
    protected $fillable = [
        'shop_id','invoice_no','project_id','quotation_id','lead_id','customer_id',
        'customer_name','mobile','address','equipment_list',
        'labour_cost','installation_cost','transport_cost','discount','tax',
        'grand_total','paid_amount','status','invoice_date','due_date','notes',
    ];
    protected $casts = [
        'equipment_list'    => 'array',
        'labour_cost'       => 'float',
        'installation_cost' => 'float',
        'transport_cost'    => 'float',
        'discount'          => 'float',
        'tax'               => 'float',
        'grand_total'       => 'float',
        'paid_amount'       => 'float',
        'invoice_date'      => 'date',
        'due_date'          => 'date',
    ];

    public static function nextInvoiceNo(): string
    {
        $yymm   = now()->format('ym');
        $shopId = session('shop_id');
        $last   = static::withoutGlobalScope('shop')
                        ->where('shop_id', $shopId)
                        ->where('invoice_no', 'like', "INV-{$yymm}%")
                        ->max('invoice_no');
        $seq = $last ? ((int)substr($last, -3) + 1) : 1;
        return 'INV-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, $this->grand_total - $this->paid_amount);
    }

    public function project()   { return $this->belongsTo(CctvProject::class, 'project_id'); }
    public function quotation() { return $this->belongsTo(CctvQuotation::class, 'quotation_id'); }
    public function lead()      { return $this->belongsTo(CctvLead::class, 'lead_id'); }
    public function customer()  { return $this->belongsTo(Customer::class); }
}
