<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DeviceList extends Model
{
    protected $fillable = ['device_name'];

    public function brands()      { return $this->hasMany(DeviceBrand::class,     'device_list_id'); }
    public function faults()      { return $this->hasMany(DeviceFault::class,     'device_list_id'); }
    public function accessories() { return $this->hasMany(DeviceAccessory::class, 'device_list_id'); }
}
