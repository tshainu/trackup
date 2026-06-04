<?php
namespace App\Models;
use App\Traits\ShopScoped;
use Illuminate\Database\Eloquent\Model;

class CctvAmcVisit extends Model
{
    use ShopScoped;
    protected $table = 'cctv_amc_visits';
    protected $fillable = ['shop_id', 'amc_id', 'visit_date', 'technician_id', 'notes', 'status'];
    protected $casts = ['visit_date' => 'date'];

    public function amc()        { return $this->belongsTo(CctvAmcContract::class, 'amc_id'); }
    public function technician() { return $this->belongsTo(Employee::class, 'technician_id'); }
}
