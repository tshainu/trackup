<?php
namespace App\Http\Controllers;

use App\Models\DeviceList;
use App\Models\DeviceBrand;
use App\Models\DeviceFault;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function brands(Request $request)
    {
        $device = DeviceList::where('device_name', $request->device_name)->first();
        if (!$device) return response()->json([]);
        return response()->json($device->brands()->select('id','device_brand')->get());
    }

    public function faults(Request $request)
    {
        $device = DeviceList::where('device_name', $request->device_name)->first();
        if (!$device) return response()->json([]);
        return response()->json($device->faults()->select('id','device_fault')->get());
    }
}
