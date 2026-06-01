<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Shop;
use App\Models\Employee;
use App\Models\JobCard;
use App\Models\FieldComplaint;
use App\Models\InvoiceItem;
use App\Models\PaymentLog;

class AdminController extends Controller
{
    // ── Auth ──────────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'shop_code' => 'required|string',
            'username'  => 'required|string',
            'password'  => 'required|string',
        ]);

        $shop = Shop::where('shop_code', strtoupper($request->shop_code))
                    ->where('status', 'active')
                    ->first();

        if (!$shop) {
            return response()->json(['error' => 'Shop not found or inactive'], 401);
        }

        if ($shop->admin_username !== $request->username) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Support both hashed and plain passwords
        $valid = Hash::check($request->password, $shop->admin_password_hash)
                 || $request->password === $shop->admin_plain_password;

        if (!$valid) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate admin token stored on shop
        $token = Str::random(60);
        $shop->update(['admin_api_token' => $token]);

        return response()->json([
            'token'   => $token,
            'role'    => 'admin',
            'shop'    => [
                'id'        => $shop->id,
                'name'      => $shop->shop_name,
                'code'      => $shop->shop_code,
                'logo'      => $shop->logo,
                'modules'   => $shop->modules ?? [],
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $shop = $request->attributes->get('auth_shop');
        $shop->update(['admin_api_token' => null]);
        return response()->json(['message' => 'Logged out']);
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        $shopId = $request->attributes->get('auth_shop')->id;

        $jobs   = JobCard::where('shop_id', $shopId);
        $fields = FieldComplaint::where('shop_id', $shopId);

        $today = now()->toDateString();

        $jobStats = [
            'total'        => (clone $jobs)->count(),
            'pending'      => (clone $jobs)->where('status', 'Pending')->count(),
            'in_progress'  => (clone $jobs)->where('status', 'In Progress')->count(),
            'completed'    => (clone $jobs)->where('status', 'Completed')->count(),
            'today'        => (clone $jobs)->whereDate('created_at', $today)->count(),
            'revenue_today'=> (clone $jobs)->whereDate('created_at', $today)->sum('paid_amount'),
            'revenue_total'=> (clone $jobs)->sum('paid_amount'),
        ];

        $fieldStats = [
            'total'       => (clone $fields)->count(),
            'pending'     => (clone $fields)->where('status', 'Pending')->count(),
            'in_progress' => (clone $fields)->where('status', 'In Progress')->count(),
            'completed'   => (clone $fields)->where('status', 'Completed')->count(),
            'today'       => (clone $fields)->whereDate('created_at', $today)->count(),
        ];

        $recentJobs = (clone $jobs)->with('employee:id,employee_name')
                                   ->orderByDesc('id')->limit(10)
                                   ->get(['id','order_no','customer_name','device_name','status','priority','date','employee_id']);

        return response()->json([
            'job_stats'    => $jobStats,
            'field_stats'  => $fieldStats,
            'recent_jobs'  => $recentJobs,
        ]);
    }

    // ── Job Cards ─────────────────────────────────────────────────────────────

    public function jobCards(Request $request)
    {
        $shopId = $request->attributes->get('auth_shop')->id;
        $status = $request->query('status');
        $search = $request->query('search');
        $perPage= (int)$request->query('per_page', 20);

        $q = JobCard::where('shop_id', $shopId)->with('employee:id,employee_name')->orderByDesc('id');

        if ($status) $q->where('status', $status);
        if ($search) {
            $q->where(function($qq) use ($search) {
                $qq->where('customer_name', 'like', "%$search%")
                   ->orWhere('phone_no', 'like', "%$search%")
                   ->orWhere('order_no', 'like', "%$search%")
                   ->orWhere('device_name', 'like', "%$search%");
            });
        }

        return response()->json($q->paginate($perPage));
    }

    public function createJobCard(Request $request)
    {
        $shopId = $request->attributes->get('auth_shop')->id;

        $data = $request->validate([
            'customer_name'     => 'required|string',
            'phone_no'          => 'required|string',
            'customer_address'  => 'nullable|string',
            'device_name'       => 'required|string',
            'device_brand'      => 'nullable|string',
            'serial_no'         => 'nullable|string',
            'device_fault'      => 'nullable|string',
            'rupees'            => 'nullable|numeric',
            'advance_amount'    => 'nullable|numeric',
            'discount'          => 'nullable|numeric',
            'employee_id'       => 'nullable|integer',
            'priority'          => 'nullable|string',
            'estimated_delivery'=> 'nullable|date',
            'accessories'       => 'nullable|string',
            'remark'            => 'nullable|string',
        ]);

        $data['shop_id']  = $shopId;
        $data['order_no'] = JobCard::nextOrderNo();
        $data['status']   = 'Pending';
        $data['date']     = now()->toDateString();

        $job = JobCard::create($data);
        $job->load('employee:id,employee_name');

        return response()->json(['message' => 'Job card created', 'job' => $job], 201);
    }

    public function showJobCard(Request $request, $id)
    {
        $shopId = $request->attributes->get('auth_shop')->id;
        $job = JobCard::where('shop_id', $shopId)->with(['employee:id,employee_name', 'invoiceItems', 'paymentLogs'])->findOrFail($id);
        return response()->json(['job' => $job]);
    }

    public function updateJobCard(Request $request, $id)
    {
        $shopId = $request->attributes->get('auth_shop')->id;
        $job = JobCard::where('shop_id', $shopId)->findOrFail($id);

        $data = $request->only([
            'customer_name','phone_no','customer_address','device_name','device_brand',
            'serial_no','device_fault','rupees','advance_amount','discount','paid_amount',
            'employee_id','priority','estimated_delivery','accessories','remark',
            'status','payment_status','payment_received',
        ]);

        $job->update($data);
        $job->load(['employee:id,employee_name', 'invoiceItems']);

        return response()->json(['message' => 'Updated', 'job' => $job]);
    }

    public function updateJobStatus(Request $request, $id)
    {
        $shopId = $request->attributes->get('auth_shop')->id;
        $job = JobCard::where('shop_id', $shopId)->findOrFail($id);

        $request->validate(['status' => 'required|string']);

        $allowed = ['Pending','In Progress','Completed','Not Completed','Broken','Cancelled'];
        if (!in_array($request->status, $allowed)) {
            return response()->json(['error' => 'Invalid status'], 422);
        }

        $update = ['status' => $request->status];
        if ($request->status === 'Cancelled') {
            $update['cancelled_reason'] = $request->input('reason', '');
            $update['cancelled_at']     = now();
        }
        if ($request->has('remark')) $update['remark'] = $request->remark;

        $job->update($update);
        return response()->json(['message' => 'Status updated', 'job' => $job]);
    }

    // ── Field Complaints ──────────────────────────────────────────────────────

    public function fieldComplaints(Request $request)
    {
        $shopId = $request->attributes->get('auth_shop')->id;
        $status = $request->query('status');
        $search = $request->query('search');
        $perPage= (int)$request->query('per_page', 20);

        $q = FieldComplaint::where('shop_id', $shopId)
                           ->with('assignedEmployee:id,employee_name')
                           ->orderByDesc('id');

        if ($status) $q->where('status', $status);
        if ($search) {
            $q->where(function($qq) use ($search) {
                $qq->where('customer_name', 'like', "%$search%")
                   ->orWhere('phone_no', 'like', "%$search%")
                   ->orWhere('complaint_no', 'like', "%$search%");
            });
        }

        return response()->json($q->paginate($perPage));
    }

    public function createFieldComplaint(Request $request)
    {
        $shopId = $request->attributes->get('auth_shop')->id;

        $data = $request->validate([
            'customer_name'    => 'required|string',
            'phone_no'         => 'required|string',
            'address'          => 'nullable|string',
            'service_type_name'=> 'nullable|string',
            'description'      => 'nullable|string',
            'priority'         => 'nullable|string',
            'assigned_to'      => 'nullable|integer',
            'scheduled_date'   => 'nullable|date',
            'gps_lat'          => 'nullable|numeric',
            'gps_lng'          => 'nullable|numeric',
        ]);

        $data['shop_id']      = $shopId;
        $data['complaint_no'] = FieldComplaint::nextComplaintNo();
        $data['status']       = $data['assigned_to'] ? 'Assigned' : 'Pending';
        if ($data['assigned_to'] ?? null) {
            $data['assigned_at'] = now();
        }

        $fc = FieldComplaint::create($data);
        return response()->json(['message' => 'Field complaint created', 'complaint' => $fc], 201);
    }

    public function showFieldComplaint(Request $request, $id)
    {
        $shopId = $request->attributes->get('auth_shop')->id;
        $fc = FieldComplaint::where('shop_id', $shopId)->with(['assignedEmployee:id,employee_name', 'items'])->findOrFail($id);
        return response()->json(['complaint' => $fc]);
    }

    public function updateFieldComplaint(Request $request, $id)
    {
        $shopId = $request->attributes->get('auth_shop')->id;
        $fc = FieldComplaint::where('shop_id', $shopId)->findOrFail($id);

        $data = $request->only([
            'customer_name','phone_no','address','service_type_name','description',
            'priority','assigned_to','scheduled_date','status','service_charge',
            'discount','paid_amount','advance_amount','payment_status','payment_received','remark',
        ]);

        // If assigning for the first time
        if (!empty($data['assigned_to']) && !$fc->assigned_to) {
            $data['assigned_at'] = now();
            if ($fc->status === 'Pending') $data['status'] = 'Assigned';
        }

        $fc->update($data);
        return response()->json(['message' => 'Updated', 'complaint' => $fc]);
    }

    // ── Employees list (for assignment dropdowns) ─────────────────────────────

    public function employees(Request $request)
    {
        $shopId = $request->attributes->get('auth_shop')->id;
        $emps = Employee::where('shop_id', $shopId)
                        ->whereIn('status', ['Active','active'])
                        ->get(['id','employee_name','role','type','phone','photo']);
        return response()->json(['employees' => $emps]);
    }
}
