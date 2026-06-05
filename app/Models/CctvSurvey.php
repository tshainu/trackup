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
        // Meta
        'survey_type', 'survey_mode',
        // Section 1 – Customer
        'contact_person', 'alt_mobile', 'email', 'gps_location', 'customer_type', 'customer_type_other',
        // Section 2 – Site
        'building_name', 'building_type', 'site_size', 'existing_security_system', 'construction_status',
        // Section 3 – Purposes
        'purposes',
        // Section 4 – Camera Locations
        'camera_locations',
        // Section 5 – Network
        'internet_status', 'isp', 'isp_other', 'wifi_coverage', 'lan_available',
        // Section 6 – Power
        'power_availability', 'ups_required', 'electrical_work_required', 'voltage_issues',
        // Section 7 – Installation
        'cable_route', 'ceiling_type', 'wall_type', 'ladder_required', 'scaffolding_required',
        'height_risk', 'special_safety_equipment',
        // Section 8 – Material Estimation
        'cameras_qty', 'dvr_channels', 'hdd_storage_days', 'cable_meters', 'accessories',
        // Section 10 – Risks
        'risks',
    ];

    protected $casts = [
        'site_photos'               => 'array',
        'internet_available'        => 'boolean',
        'existing_cctv'             => 'boolean',
        'existing_security_system'  => 'boolean',
        'wifi_coverage'             => 'boolean',
        'lan_available'             => 'boolean',
        'ups_required'              => 'boolean',
        'electrical_work_required'  => 'boolean',
        'voltage_issues'            => 'boolean',
        'ladder_required'           => 'boolean',
        'scaffolding_required'      => 'boolean',
        'purposes'                  => 'array',
        'camera_locations'          => 'array',
        'accessories'               => 'array',
        'risks'                     => 'array',
        'survey_date'               => 'date',
    ];

    public static function nextSurveyNo(): string
    {
        $yymm   = now()->format('ym');
        $shopId = session('shop_id');
        $last   = static::withoutGlobalScope('shop')
                        ->where('shop_id', $shopId)
                        ->where('survey_no', 'like', "SRV-{$yymm}%")
                        ->max('survey_no');
        $seq = $last ? ((int) substr($last, -3) + 1) : 1;
        return 'SRV-' . $yymm . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function lead()       { return $this->belongsTo(CctvLead::class, 'lead_id'); }
    public function customer()   { return $this->belongsTo(Customer::class); }
    public function technician() { return $this->belongsTo(Employee::class, 'technician_id'); }
}
