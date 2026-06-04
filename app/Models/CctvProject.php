<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvProject extends Model
{
    use ShopScoped;
    protected $table = 'cctv_projects';
    protected $fillable = [
        'shop_id', 'project_no', 'lead_id', 'quotation_id', 'customer_id',
        'customer_name', 'mobile', 'address', 'installation_date', 'completion_date',
        'team_assigned', 'signature_path', 'stage', 'notes',
    ];
    protected $casts = [
        'team_assigned'     => 'array',
        'installation_date' => 'date',
        'completion_date'   => 'date',
    ];

    public static function nextProjectNo(): string
    {
        $yymm = now()->format('ym');
        $shopId = session('shop_id');
        $last = static::withoutGlobalScope('shop')->where('shop_id', $shopId)
                      ->where('project_no', 'like', "PRJ-{$yymm}%")->max('project_no');
        $seq = $last ? ((int)substr($last, -3) + 1) : 1;
        return 'PRJ-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public static function stages(): array
    {
        return ['Survey Complete', 'Materials Ready', 'Installation Started', 'Configuration', 'Testing', 'Customer Handover', 'Warranty Activated'];
    }

    public function lead()      { return $this->belongsTo(CctvLead::class, 'lead_id'); }
    public function quotation() { return $this->belongsTo(CctvQuotation::class, 'quotation_id'); }
    public function customer()  { return $this->belongsTo(Customer::class); }
    public function assets()    { return $this->hasMany(CctvAsset::class, 'project_id'); }
    public function amcContracts() { return $this->hasMany(CctvAmcContract::class, 'project_id'); }
}
