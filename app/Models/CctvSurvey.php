<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvSurvey extends Model
{
    use ShopScoped;
    protected $table = 'cctv_surveys';
    protected $fillable = [
        'shop_id', 'survey_no', 'lead_id', 'customer_id', 'customer_name', 'mobile',
        'survey_date', 'technician_id', 'site_photos', 'num_floors',
        'indoor_cameras', 'outdoor_cameras', 'internet_available',
        'existing_cctv', 'special_notes', 'status',
    ];
    protected $casts = [
        'site_photos'      => 'array',
        'internet_available' => 'boolean',
        'existing_cctv'    => 'boolean',
        'survey_date'      => 'date',
    ];

    public static function nextSurveyNo(): string
    {
        $yymm = now()->format('ym');
        $shopId = session('shop_id');
        $last = static::withoutGlobalScope('shop')->where('shop_id', $shopId)
                      ->where('survey_no', 'like', "SRV-{$yymm}%")->max('survey_no');
        $seq = $last ? ((int)substr($last, -3) + 1) : 1;
        return 'SRV-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function lead()       { return $this->belongsTo(CctvLead::class, 'lead_id'); }
    public function customer()   { return $this->belongsTo(Customer::class); }
    public function technician() { return $this->belongsTo(Employee::class, 'technician_id'); }
}
