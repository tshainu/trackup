<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceList;
use App\Models\DeviceBrand;
use App\Models\DeviceFault;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = DeviceList::with(['brands','faults'])->get();
        return view('admin.devices.index', compact('devices'));
    }

    // Device types
    public function storeDevice(Request $request)
    {
        $request->validate(['device_name' => 'required|string|unique:device_lists,device_name|max:100']);
        $device = DeviceList::create(['device_name' => $request->device_name]);

        if ($request->expectsJson()) {
            return response()->json(['id' => $device->id, 'device_name' => $device->device_name]);
        }
        return back()->with('success', 'Device type added.');
    }

    public function destroyDevice(DeviceList $device)
    {
        $device->delete();
        if (request()->expectsJson()) return response()->json(['ok' => true]);
        return back()->with('success', 'Device type deleted.');
    }

    // Brands
    public function storeBrand(Request $request)
    {
        $request->validate([
            'device_list_id' => 'required|exists:device_lists,id',
            'device_brand'   => 'required|string|max:100',
        ]);
        $brand = DeviceBrand::create($request->only('device_list_id', 'device_brand'));

        if ($request->expectsJson()) {
            return response()->json(['id' => $brand->id, 'device_brand' => $brand->device_brand]);
        }
        return back()->with('success', 'Brand added.');
    }

    public function destroyBrand(DeviceBrand $brand)
    {
        $brand->delete();
        if (request()->expectsJson()) return response()->json(['ok' => true]);
        return back()->with('success', 'Brand deleted.');
    }

    // Faults
    public function storeFault(Request $request)
    {
        $request->validate([
            'device_list_id' => 'required|exists:device_lists,id',
            'device_fault'   => 'required|string|max:100',
        ]);
        $fault = DeviceFault::create($request->only('device_list_id', 'device_fault'));

        if ($request->expectsJson()) {
            return response()->json(['id' => $fault->id, 'device_fault' => $fault->device_fault]);
        }
        return back()->with('success', 'Fault added.');
    }

    public function destroyFault(DeviceFault $fault)
    {
        $fault->delete();
        if (request()->expectsJson()) return response()->json(['ok' => true]);
        return back()->with('success', 'Fault deleted.');
    }
}
