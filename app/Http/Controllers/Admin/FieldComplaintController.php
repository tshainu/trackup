<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FieldComplaint;
use App\Models\FieldComplaintItem;
use App\Models\FieldPaymentLog;
use App\Models\ServiceType;
use App\Models\Employee;
use App\Services\SmsService;
use Illuminate\Http\Request;

class FieldComplaintController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'all');
        $search = $request->get('q');

        $query = FieldComplaint::with(['assignedEmployee','serviceType'])
            ->orderByRaw("CASE status
                WHEN 'Pending'     THEN 1
                WHEN 'Assigned'    THEN 2
                WHEN 'In Progress' THEN 3
                WHEN 'Completed'   THEN 4
                WHEN 'Billed'      THEN 5
                WHEN 'Cancelled'   THEN 6
                ELSE 7 END")
            ->latest();

        if ($tab !== 'all') {
            $statusMap = [
                'pending'     => 'Pending',
                'assigned'    => 'Assigned',
                'inprogress'  => 'In Progress',
                'completed'   => 'Completed',
                'billed'      => 'Billed',
                'cancelled'   => 'Cancelled',
            ];
            if (isset($statusMap[$tab])) {
                $query->where('status', $statusMap[$tab]);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('complaint_no',   'like', "%{$search}%")
                  ->orWhere('customer_name','like', "%{$search}%")
                  ->orWhere('phone_no',     'like', "%{$search}%")
                  ->orWhere('address',      'like', "%{$search}%");
            });
        }

        $complaints = $query->paginate(20)->withQueryString();

        $counts = [
            'all'        => FieldComplaint::count(),
            'pending'    => FieldComplaint::where('status','Pending')->count(),
            'assigned'   => FieldComplaint::where('status','Assigned')->count(),
            'inprogress' => FieldComplaint::where('status','In Progress')->count(),
            'completed'  => FieldComplaint::where('status','Completed')->count(),
            'billed'     => FieldComplaint::where('status','Billed')->count(),
        ];

        return view('admin.field-complaints.index', compact('complaints','tab','search','counts'));
    }

    // ── Create ────────────────────────────────────────────────────────────────
    public function create()
    {
        $serviceTypes = ServiceType::where('active', true)->orderBy('name')->get();
        $employees    = Employee::where('status', 'active')->orderBy('employee_name')->get();
        return view('admin.field-complaints.create', compact('serviceTypes', 'employees'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'        => 'required|string|max:150',
            'phone_no'             => 'required|string|max:20',
            'address'              => 'nullable|string',
            'location_notes'       => 'nullable|string',
            'gps_raw'              => 'nullable|string',
            'gps_lat'              => 'nullable|numeric',
            'gps_lng'              => 'nullable|numeric',
            'gps_label'            => 'nullable|string|max:100',
            'service_type_id'      => 'nullable|exists:service_types,id',
            'description'          => 'nullable|string',
            'priority'             => 'required|in:Low,Normal,High,Urgent',
            'scheduled_date'       => 'nullable|date',
            'advance_amount'       => 'nullable|numeric|min:0',
            'remark'               => 'nullable|string',
            'customer_db_id'       => 'nullable|exists:customers,id',
            'assigned_to'          => 'nullable|exists:employees,id',
            'assign_scheduled_date'=> 'nullable|date',
        ]);

        $advance     = (float)($request->advance_amount ?? 0);
        $serviceType = $request->service_type_id ? ServiceType::find($request->service_type_id) : null;

        // Resolve GPS — if raw link pasted, try to parse it
        $lat = $request->gps_lat ? (float)$request->gps_lat : null;
        $lng = $request->gps_lng ? (float)$request->gps_lng : null;
        if ((!$lat || !$lng) && $request->filled('gps_raw')) {
            $parsed = Customer::parseGpsLink($request->gps_raw);
            if ($parsed) { $lat = $parsed['lat']; $lng = $parsed['lng']; }
        }

        // Upsert customer in shared DB
        $customer = null;
        if ($request->customer_db_id) {
            $customer = Customer::find($request->customer_db_id);
        }
        if (!$customer) {
            $customer = Customer::where('phone', $request->phone_no)->first();
        }

        $customerData = [
            'name'    => $request->customer_name,
            'phone'   => $request->phone_no,
            'address' => $request->address,
        ];
        if ($lat && $lng) {
            $customerData['gps_lat']      = $lat;
            $customerData['gps_lng']      = $lng;
            $customerData['gps_label']    = $request->gps_label ?: 'Site';
            $customerData['gps_raw_link'] = $request->gps_raw;
        }

        if ($customer) {
            $customer->update($customerData);
        } else {
            $customer = Customer::create(array_merge($customerData, [
                'customer_id' => Customer::nextCustomerId(),
            ]));
        }

        // Resolve assigned employee
        $assignedEmployee = null;
        $scheduledDate    = $request->scheduled_date;
        if ($request->filled('assigned_to')) {
            $assignedEmployee = Employee::find($request->assigned_to);
            // If separate assign_scheduled_date given, use it
            if ($request->filled('assign_scheduled_date')) {
                $scheduledDate = $request->assign_scheduled_date;
            }
        }

        $complaint = FieldComplaint::create([
            'complaint_no'      => FieldComplaint::nextComplaintNo(),
            'customer_db_id'    => $customer->id,
            'customer_name'     => $request->customer_name,
            'phone_no'          => $request->phone_no,
            'address'           => $request->address,
            'location_notes'    => $request->location_notes,
            'gps_lat'           => $lat,
            'gps_lng'           => $lng,
            'gps_label'         => $request->gps_label ?: ($lat ? 'Site' : null),
            'service_type_id'   => $request->service_type_id,
            'service_type_name' => $serviceType?->name,
            'description'       => $request->description,
            'priority'          => $request->priority,
            'scheduled_date'    => $scheduledDate,
            'advance_amount'    => $advance,
            'paid_amount'       => $advance,
            'payment_status'    => $advance > 0 ? 'partial' : 'unpaid',
            'service_charge'    => $serviceType?->base_charge ?? 0,
            'remark'            => $request->remark,
            'status'            => $assignedEmployee ? 'Assigned' : 'Pending',
            'assigned_to'       => $assignedEmployee?->id,
            'assigned_at'       => $assignedEmployee ? now() : null,
        ]);

        // SMS: notify customer of new field complaint
        try {
            (new SmsService())->sendTemplate('field_complaint_created', $complaint->phone_no, [
                'customer_name' => $complaint->customer_name,
                'complaint_no'  => $complaint->complaint_no,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[SMS] field_complaint_created failed: ' . $e->getMessage());
        }

        // SMS: notify assigned staff member
        if ($assignedEmployee && $assignedEmployee->phone_no_1) {
            try {
                (new SmsService())->sendTemplate('field_complaint_assigned', $assignedEmployee->phone_no_1, [
                    'employee_name'  => $assignedEmployee->employee_name,
                    'complaint_no'   => $complaint->complaint_no,
                    'customer_name'  => $complaint->customer_name,
                    'address'        => $complaint->address ?? 'N/A',
                    'scheduled_date' => $complaint->scheduled_date ?? 'TBD',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[SMS] field_complaint_assigned failed: ' . $e->getMessage());
            }
        }

        $successMsg = $assignedEmployee
            ? "Complaint {$complaint->complaint_no} logged and assigned to {$assignedEmployee->employee_name}."
            : "Complaint {$complaint->complaint_no} logged successfully.";

        return redirect()->route('admin.field-complaints.show', $complaint)
            ->with('success', $successMsg);
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show(FieldComplaint $fieldComplaint)
    {
        $fieldComplaint->load(['serviceType','assignedEmployee','items','paymentLogs']);
        $fieldStaff   = Employee::where('type','outbound')->where('status','active')->orderBy('employee_name')->get();
        $serviceTypes = ServiceType::where('active', true)->orderBy('name')->get();
        return view('admin.field-complaints.show', compact('fieldComplaint','fieldStaff','serviceTypes'));
    }

    // ── Update (edit details) ─────────────────────────────────────────────────
    public function update(Request $request, FieldComplaint $fieldComplaint)
    {
        $request->validate([
            'customer_name'   => 'required|string|max:150',
            'phone_no'        => 'required|string|max:20',
            'address'         => 'required|string',
            'location_notes'  => 'nullable|string',
            'service_type_id' => 'nullable|exists:service_types,id',
            'description'     => 'nullable|string',
            'priority'        => 'required|in:Low,Normal,High,Urgent',
            'scheduled_date'  => 'nullable|date',
            'remark'          => 'nullable|string',
            'service_charge'  => 'nullable|numeric|min:0',
            'discount'        => 'nullable|numeric|min:0',
            'items'           => 'nullable|array',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $serviceType = $request->service_type_id
            ? ServiceType::find($request->service_type_id)
            : null;

        $fieldComplaint->update([
            'customer_name'     => $request->customer_name,
            'phone_no'          => $request->phone_no,
            'address'           => $request->address,
            'location_notes'    => $request->location_notes,
            'service_type_id'   => $request->service_type_id,
            'service_type_name' => $serviceType?->name ?? $fieldComplaint->service_type_name,
            'description'       => $request->description,
            'priority'          => $request->priority,
            'scheduled_date'    => $request->scheduled_date,
            'remark'            => $request->remark,
            'service_charge'    => $request->input('service_charge', $fieldComplaint->service_charge),
            'discount'          => $request->input('discount', 0),
        ]);

        // Sync line items
        $fieldComplaint->items()->delete();
        if ($request->filled('items')) {
            foreach ($request->items as $item) {
                FieldComplaintItem::create([
                    'field_complaint_id' => $fieldComplaint->id,
                    'description'        => $item['description'],
                    'qty'                => $item['qty'],
                    'unit_price'         => $item['unit_price'],
                    'total'              => round($item['qty'] * $item['unit_price'], 2),
                ]);
            }
        }

        // Auto-update payment_received if fully paid
        $fieldComplaint->refresh()->load('items');
        if ($fieldComplaint->balance <= 0 && $fieldComplaint->paid_amount > 0) {
            $fieldComplaint->update(['payment_received' => true, 'payment_status' => 'paid']);
        }

        return redirect()->route('admin.field-complaints.show', $fieldComplaint)
            ->with('success', 'Complaint details updated.');
    }

    // ── Assign to field staff ─────────────────────────────────────────────────
    public function assign(Request $request, FieldComplaint $fieldComplaint)
    {
        $request->validate([
            'assigned_to'    => 'required|exists:employees,id',
            'scheduled_date' => 'nullable|date',
        ]);

        $employee = Employee::find($request->assigned_to);

        $fieldComplaint->update([
            'assigned_to'    => $request->assigned_to,
            'assigned_at'    => now(),
            'scheduled_date' => $request->scheduled_date ?? $fieldComplaint->scheduled_date,
            'status'         => 'Assigned',
        ]);

        return back()->with('success', "Assigned to {$employee->employee_name}. Notification sent.");
    }

    // ── Update status ─────────────────────────────────────────────────────────
    public function updateStatus(Request $request, FieldComplaint $fieldComplaint)
    {
        $request->validate([
            'status'           => 'required|in:Pending,Assigned,In Progress,Completed,Billed,Cancelled',
            'completion_notes' => 'nullable|string',
        ]);

        $data = ['status' => $request->status];
        if ($request->status === 'Completed') {
            $data['completed_at']      = now();
            $data['completion_notes']  = $request->completion_notes;
        }
        if ($request->status === 'Billed') {
            // auto-generate invoice_no if not set
            if (!$fieldComplaint->invoice_no) {
                $lastNo = FieldComplaint::whereNotNull('invoice_no')->max('invoice_no');
                $seq    = $lastNo ? ((int)filter_var($lastNo, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;
                $data['invoice_no']   = 'FI-' . now()->format('Y') . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
                $data['invoice_date'] = now()->toDateString();
            }
        }

        $oldStatus = $fieldComplaint->status;
        $fieldComplaint->update($data);

        // SMS: notify on field service completion
        if ($request->status === 'Completed' && $oldStatus !== 'Completed') {
            try {
                $technician = $fieldComplaint->assignedEmployee?->employee_name ?? 'our technician';
                (new SmsService())->sendTemplate('field_service_completed', $fieldComplaint->phone_no, [
                    'customer_name' => $fieldComplaint->customer_name,
                    'complaint_no'  => $fieldComplaint->complaint_no,
                    'technician'    => $technician,
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[SMS] field_service_completed failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Status updated to {$request->status}.");
    }

    // ── Record payment ────────────────────────────────────────────────────────
    public function recordPayment(Request $request, FieldComplaint $fieldComplaint)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'note'        => 'nullable|string|max:100',
        ]);

        $fieldComplaint->load('items');
        $amountNow   = (float)$request->amount_paid;
        $alreadyPaid = (float)$fieldComplaint->paid_amount;
        $totalPaid   = $alreadyPaid + $amountNow;
        $grandTotal  = $fieldComplaint->grand_total;
        $isFullyPaid = $totalPaid >= $grandTotal;

        FieldPaymentLog::create([
            'field_complaint_id' => $fieldComplaint->id,
            'amount'             => $amountNow,
            'note'               => $request->input('note') ?: ($alreadyPaid > 0 ? 'Instalment' : 'Payment'),
            'paid_at'            => now(),
        ]);

        $fieldComplaint->update([
            'paid_amount'      => $totalPaid,
            'payment_status'   => $isFullyPaid ? 'paid' : 'partial',
            'payment_received' => $isFullyPaid ? true : false,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'paid' => $totalPaid, 'balance' => max(0, $grandTotal - $totalPaid)]);
        }
        return back()->with('success', 'Payment of Rs. ' . number_format($amountNow, 2) . ' recorded.');
    }

    // ── Generate invoice ──────────────────────────────────────────────────────
    public function invoice(FieldComplaint $fieldComplaint)
    {
        $fieldComplaint->load(['serviceType','assignedEmployee','items','paymentLogs']);

        // Auto-assign invoice no if not set
        if (!$fieldComplaint->invoice_no) {
            $lastNo = FieldComplaint::whereNotNull('invoice_no')->max('invoice_no');
            $seq    = $lastNo ? ((int)filter_var($lastNo, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;
            $fieldComplaint->update([
                'invoice_no'   => 'FI-' . now()->format('Y') . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT),
                'invoice_date' => now()->toDateString(),
                'status'       => $fieldComplaint->status === 'Completed' ? 'Billed' : $fieldComplaint->status,
            ]);
            $fieldComplaint->refresh();
        }

        return view('admin.field-complaints.invoice', compact('fieldComplaint'));
    }

    // ── Destroy ───────────────────────────────────────────────────────────────
    public function destroy(FieldComplaint $fieldComplaint)
    {
        $no = $fieldComplaint->complaint_no;
        $fieldComplaint->delete();
        return redirect()->route('admin.field-complaints.index')
            ->with('success', "Complaint {$no} deleted.");
    }
}
