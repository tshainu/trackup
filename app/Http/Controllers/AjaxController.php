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
     * Returns array of matches when ?multi=1, single match otherwise.
     */
    public function customerLookup(Request $request)
    {
        $phone = trim($request->get('phone', ''));
        $multi = $request->boolean('multi', false);

        if (strlen($phone) < 2) {
            return $multi ? response()->json([]) : response()->json(['found' => false]);
        }

        if ($multi) {
            // Return up to 8 matches from both sources, deduplicated by phone
            $results = [];
            $seenPhones = [];

            // 1) customers table
            $customers = Customer::where('phone', 'like', "%{$phone}%")
                                  ->orderBy('name')->limit(6)->get();
            foreach ($customers as $c) {
                $key = preg_replace('/\D/', '', $c->phone);
                if (!in_array($key, $seenPhones)) {
                    $seenPhones[] = $key;
                    $results[] = [
                        'source'  => 'customers',
                        'name'    => $c->name,
                        'phone'   => $c->phone,
                        'email'   => $c->email ?? '',
                        'nic'     => $c->nic ?? '',
                        'address' => $c->address ?? '',
                        'dob'     => '',
                    ];
                }
            }

            // 2) job_cards (legacy / additional)
            $jobs = JobCard::where('phone_no', 'like', "%{$phone}%")
                            ->select('customer_name','phone_no','customer_email','customer_nic','customer_address','customer_dob')
                            ->orderByDesc('id')
                            ->limit(10)->get();
            foreach ($jobs as $j) {
                $key = preg_replace('/\D/', '', $j->phone_no ?? '');
                if ($key && !in_array($key, $seenPhones)) {
                    $seenPhones[] = $key;
                    $results[] = [
                        'source'  => 'job_cards',
                        'name'    => $j->customer_name ?? '',
                        'phone'   => $j->phone_no ?? '',
                        'email'   => $j->customer_email ?? '',
                        'nic'     => $j->customer_nic ?? '',
                        'address' => $j->customer_address ?? '',
                        'dob'     => $j->customer_dob ?? '',
                    ];
                }
                if (count($results) >= 8) break;
            }

            return response()->json($results);
        }

        // Single-match mode (legacy behaviour for field complaints, etc.)
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
