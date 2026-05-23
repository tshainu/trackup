<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\Employee;
use App\Models\DeviceList;
use App\Models\DeviceBrand;
use App\Models\DeviceFault;
use Illuminate\Http\Request;

class JobCardController extends Controller
{
    public function index(Request $request)
    {
        $query = JobCard::with('employee');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('order_no', 'like', "%$s%")
                  ->orWhere('customer_name', 'like', "%$s%")
                  ->orWhere('phone_no', 'like', "%$s%")
                  ->orWhere('serial_no', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('device')) {
            $query->where('device_name', $request->device);
        }

        $jobs = $query->latest()->paginate(20)->withQueryString();
        $devices = DeviceList::all();
        return view('admin.jobcards.index', compact('jobs', 'devices'));
    }

    public function create()
    {
        $devices   = DeviceList::all();
        $employees = Employee::where('status', 'active')->get();
        $orderNo   = JobCard::nextOrderNo();
        $customerId = JobCard::nextCustomerId();
        return view('admin.jobcards.create', compact('devices', 'employees', 'orderNo', 'customerId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_address' => 'nullable|string|max:255',
            'customer_email'   => 'nullable|email|max:255',
            'customer_nic'     => 'nullable|string|max:20',
            'customer_dob'     => 'nullable|string',
            'phone_no'         => 'required|string|max:20',
            'device_name'      => 'required|string|max:255',
            'device_brand'     => 'nullable|string|max:255',
            'serial_no'        => 'nullable|string|max:100',
            'device_age'       => 'nullable|string|max:20',
            'device_fault'     => 'nullable|string|max:255',
            'issue'            => 'nullable|string|max:500',
            'date'             => 'required|date',
            'rupees'           => 'nullable|numeric|min:0',
            'remark'           => 'nullable|string|max:500',
            'need_assistant'   => 'nullable|boolean',
            'employee_id'      => 'nullable|exists:employees,id',
        ]);

        $validated['order_no']    = JobCard::nextOrderNo();
        $validated['customer_id'] = JobCard::nextCustomerId();
        $validated['status']      = 'Pending';
        $validated['need_assistant'] = $request->has('need_assistant') ? 1 : 0;

        JobCard::create($validated);

        return redirect()->route('admin.jobcards.index')
                         ->with('success', 'Job order created successfully.');
    }

    public function show(JobCard $jobCard)
    {
        $jobCard->load('employee');
        return view('admin.jobcards.show', compact('jobCard'));
    }

    public function edit(JobCard $jobCard)
    {
        $devices   = DeviceList::all();
        $employees = Employee::where('status', 'active')->get();
        $brands    = DeviceBrand::whereHas('deviceList', fn($q) => $q->where('device_name', $jobCard->device_name))->get();
        $faults    = DeviceFault::whereHas('deviceList', fn($q) => $q->where('device_name', $jobCard->device_name))->get();
        return view('admin.jobcards.edit', compact('jobCard', 'devices', 'employees', 'brands', 'faults'));
    }

    public function update(Request $request, JobCard $jobCard)
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_address' => 'nullable|string|max:255',
            'customer_email'   => 'nullable|email|max:255',
            'customer_nic'     => 'nullable|string|max:20',
            'customer_dob'     => 'nullable|string',
            'phone_no'         => 'required|string|max:20',
            'device_name'      => 'required|string|max:255',
            'device_brand'     => 'nullable|string|max:255',
            'serial_no'        => 'nullable|string|max:100',
            'device_age'       => 'nullable|string|max:20',
            'device_fault'     => 'nullable|string|max:255',
            'issue'            => 'nullable|string|max:500',
            'date'             => 'required|date',
            'rupees'           => 'nullable|numeric|min:0',
            'status'           => 'required|in:Pending,In Progress,Completed,Not Completed',
            'remark'           => 'nullable|string|max:500',
            'need_assistant'   => 'nullable|boolean',
            'employee_id'      => 'nullable|exists:employees,id',
        ]);
        $validated['need_assistant'] = $request->has('need_assistant') ? 1 : 0;

        $jobCard->update($validated);

        return redirect()->route('admin.jobcards.index')
                         ->with('success', 'Job order updated successfully.');
    }

    public function destroy(JobCard $jobCard)
    {
        $jobCard->delete();
        return redirect()->route('admin.jobcards.index')
                         ->with('success', 'Job order deleted.');
    }

    public function track(Request $request)
    {
        $job = null;
        $search = $request->input('q');
        if ($search) {
            $job = JobCard::with('employee')
                ->where('order_no', $search)
                ->orWhere('serial_no', $search)
                ->first();
        }
        return view('admin.jobcards.track', compact('job', 'search'));
    }
}
