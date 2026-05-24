<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DeviceAccessory extends Model
{
    protected $fillable = ['device_list_id', 'accessory_name'];

    public function deviceList()
    {
        return $this->belongsTo(DeviceList::class, 'device_list_id');
    }
}
