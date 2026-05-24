<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeviceList;
use App\Models\DeviceBrand;
use App\Models\DeviceAccessory;
use App\Models\DeviceFault;
use App\Models\JobCard;
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

    public function accessories(Request $request)
    {
        return response()->json(
            DeviceAccessory::orderBy('accessory_name')->select('id','accessory_name')->get()
        );
    }

    /**
     * Customer lookup by phone — searches customers table first,
     * then falls back to job_cards for legacy records.
     */
    public function customerLookup(Request $request)
    {
        $phone = trim($request->get('phone', ''));
        if (strlen($phone) < 3) {
            return response()->json(['found' => false]);
        }

        // 1) Check shared customers table
        $customer = Customer::where('phone', 'like', "%{$phone}%")->first();
        if ($customer) {
            return response()->json([
                'found'       => true,
                'source'      => 'customers',
                'customer_id' => $customer->id,
                'name'        => $customer->name,
                'phone'       => $customer->phone,
                'email'       => $customer->email ?? '',
                'address'     => $customer->address ?? '',
                'gps_lat'     => $customer->gps_lat,
                'gps_lng'     => $customer->gps_lng,
                'gps_label'   => $customer->gps_label ?? '',
                'gps_link'    => $customer->hasGps() ? $customer->googleMapsUrl() : null,
                'visit_count' => $customer->fieldComplaints()->count(),
            ]);
        }

        // 2) Fallback: check job_cards (legacy data)
        $job = JobCard::where('phone_no', 'like', "%{$phone}%")
                      ->orderByDesc('id')->first();
        if ($job) {
            return response()->json([
                'found'       => true,
                'source'      => 'job_cards',
                'customer_id' => null,
                'name'        => $job->customer_name,
                'phone'       => $job->phone_no,
                'email'       => $job->customer_email ?? '',
                'address'     => $job->customer_address ?? '',
                'gps_lat'     => null,
                'gps_lng'     => null,
                'gps_label'   => '',
                'gps_link'    => null,
                'visit_count' => 0,
            ]);
        }

        return response()->json(['found' => false]);
    }
}
