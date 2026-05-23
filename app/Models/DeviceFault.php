<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DeviceFault extends Model
{
    protected $fillable = ['device_list_id','device_fault'];
    public function deviceList() { return $this->belongsTo(DeviceList::class, 'device_list_id'); }
}
