<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\DeliveredOrder;
use App\Models\Employee;
use App\Models\DeviceList;
use App\Models\DeviceBrand;
use App\Models\DeviceFault;
use App\Services\SmsService;
use App\Models\LabelSetting;
use App\Models\Shop;
use Illuminate\Http\Request;

class JobCardController extends Controller
{
    public function index(Request $request)
    {
        // Delivered orders live in a separate table — redirect to dedicated page
        if ($request->input('status') === 'Delivered') {
            return redirect()->route('admin.jobcards.delivered', $request->except('status'));
        }

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

        $sortable = ['order_no','customer_name','phone_no','device_name','date','rupees','status'];
        $sort  = in_array($request->sort, $sortable) ? $request->sort : 'id';
        $dir   = $request->dir === 'asc' ? 'asc' : 'desc';

        $jobs = $query->orderBy($sort, $dir)->paginate(20)->withQueryString();
        $devices = DeviceList::all();
        $labelSettings = LabelSetting::current();
        $shop = Shop::find(session('shop_id'));
        $shopName = $shop ? $shop->shop_name : config('app.name');
        return view('admin.jobcards.index', compact('jobs', 'devices', 'sort', 'dir', 'labelSettings', 'shopName'));
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
            'device_fault'       => 'nullable|string|max:255',
            'item_description'   => 'nullable|string|max:500',
            'issue'              => 'nullable|string|max:500',
            'date'               => 'required|date',
            'rupees'             => 'nullable|numeric|min:0',
            'advance_amount'     => 'nullable|numeric|min:0',
            'remark'             => 'nullable|string|max:500',
            'need_assistant'     => 'nullable|boolean',
            'employee_id'        => 'nullable|exists:employees,id',
            'priority'           => 'nullable|in:Low,Normal,High,Urgent',
            'estimated_delivery' => 'nullable|date',
            'accessories'        => 'nullable|string',
        ]);

        $validated['order_no']       = JobCard::nextOrderNo();
        $validated['customer_id']    = JobCard::nextCustomerId();
        $validated['status']         = 'Pending';
        $validated['priority']       = $request->input('priority', 'Normal');
        $validated['need_assistant'] = $request->has('need_assistant') ? 1 : 0;

        // Advance amount counts as initial paid_amount
        $advance = (float)($validated['advance_amount'] ?? 0);
        if ($advance > 0) {
            $validated['paid_amount']     = $advance;
            $validated['payment_status']  = 'partial';
        }

        $job = JobCard::create($validated);

        // SMS: notify customer of new job order
        try {
            (new SmsService())->sendTemplate('job_created', $job->phone_no, [
                'customer_name' => $job->customer_name,
                'order_no'      => $job->order_no,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[SMS] job_created failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.jobcards.index')
                         ->with('success', 'Job order created successfully.');
    }

    public function show(Request $request, JobCard $jobCard)
    {
        $jobCard->load('employee');
        if ($request->expectsJson()) {
            return response()->json($jobCard);
        }
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
            'device_fault'       => 'nullable|string|max:255',
            'item_description'   => 'nullable|string|max:500',
            'issue'              => 'nullable|string|max:500',
            'date'               => 'required|date',
            'rupees'             => 'nullable|numeric|min:0',
            'status'             => 'required|in:Pending,In Progress,Completed,Not Completed,Broken,Cancelled',
            'cancel_reason'      => 'nullable|string|max:500',
            'remark'             => 'nullable|string|max:500',
            'need_assistant'     => 'nullable|boolean',
            'employee_id'        => 'nullable|exists:employees,id',
            'priority'           => 'nullable|in:Low,Normal,High,Urgent',
            'estimated_delivery' => 'nullable|date',
            'accessories'        => 'nullable|string',
        ]);
        $validated['need_assistant']   = $request->has('need_assistant') ? 1 : 0;
        $validated['payment_received'] = $request->has('payment_received') ? 1 : 0;
        $validated['priority'] = $request->input('priority', 'Normal');

        if ($validated['status'] === 'Cancelled' && $jobCard->status !== 'Cancelled') {
            $validated['cancelled_reason'] = $request->input('cancel_reason');
            $validated['cancelled_at']     = now();
        }

        $oldStatus = $jobCard->status;
        $jobCard->update($validated);

        // SMS: notify on Completed or Broken
        if (in_array($validated['status'], ['Completed', 'Broken']) && $oldStatus !== $validated['status']) {
            try {
                (new SmsService())->sendTemplate('job_status_changed', $jobCard->phone_no, [
                    'customer_name' => $jobCard->customer_name,
                    'order_no'      => $jobCard->order_no,
                    'status'        => $validated['status'],
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[SMS] job_status_changed failed: ' . $e->getMessage());
            }
        }

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

    public function deliveredIndex(Request $request)
    {
        $query = DeliveredOrder::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('order_no',      'like', "%$s%")
                  ->orWhere('customer_name','like', "%$s%")
                  ->orWhere('phone_no',     'like', "%$s%")
                  ->orWhere('device_name',  'like', "%$s%");
            });
        }
        if ($request->filled('device')) {
            $query->where('device_name', $request->device);
        }

        $sort  = in_array($request->sort, ['order_no','customer_name','device_name','delivered_at','grand_total']) ? $request->sort : 'delivered_at';
        $dir   = $request->dir === 'asc' ? 'asc' : 'desc';

        $orders  = $query->orderBy($sort, $dir)->paginate(20)->withQueryString();
        $devices = DeliveredOrder::select('device_name')->distinct()->pluck('device_name');

        return view('admin.jobcards.delivered', compact('orders', 'devices', 'sort', 'dir'));
    }

    public function quickStatus(Request $request, JobCard $jobCard)
    {
        $allowed = ['Pending', 'In Progress', 'Completed', 'Not Completed', 'Broken', 'Delivered'];
        $status  = $request->input('status');

        if (!in_array($status, $allowed)) {
            return response()->json(['error' => 'Invalid status'], 422);
        }

        if ($status === 'Delivered') {
            // Archive to delivered_orders
            $jobCard->load('invoiceItems');
            $invoiceItemsSnapshot = $jobCard->invoiceItems->map(fn($i) => [
                'description' => $i->description,
                'quantity'    => $i->quantity ?? $i->qty ?? 1,
                'unit_price'  => $i->unit_price,
                'total'       => $i->total,
            ])->toArray();

            DeliveredOrder::create([
                'order_no'           => $jobCard->order_no,
                'invoice_no'         => $jobCard->invoice_no,
                'invoice_date'       => $jobCard->invoice_date,
                'customer_id'        => $jobCard->customer_id,
                'customer_name'      => $jobCard->customer_name,
                'customer_address'   => $jobCard->customer_address,
                'customer_email'     => $jobCard->customer_email,
                'customer_nic'       => $jobCard->customer_nic,
                'customer_dob'       => $jobCard->customer_dob,
                'phone_no'           => $jobCard->phone_no,
                'device_name'        => $jobCard->device_name,
                'device_brand'       => $jobCard->device_brand,
                'serial_no'          => $jobCard->serial_no,
                'device_age'         => $jobCard->device_age,
                'device_fault'       => $jobCard->device_fault,
                'item_description'   => $jobCard->item_description,
                'issue'              => $jobCard->issue,
                'date'               => $jobCard->date,
                'rupees'             => $jobCard->rupees,
                'discount'           => $jobCard->discount,
                'paid_amount'        => $jobCard->paid_amount,
                'grand_total'        => $jobCard->grand_total,
                'status'             => 'Delivered',
                'priority'           => $jobCard->priority,
                'estimated_delivery' => $jobCard->estimated_delivery,
                'accessories'        => $jobCard->accessories,
                'remark'             => $jobCard->remark,
                'need_assistant'     => $jobCard->need_assistant,
                'employee_id'        => $jobCard->employee_id,
                'payment_received'   => true,
                'invoice_items'      => $invoiceItemsSnapshot,
                'delivered_at'       => now(),
            ]);

            $jobCard->invoiceItems()->delete();
            $jobCard->delete();

            return response()->json(['ok' => true, 'status' => 'Delivered', 'archived' => true]);
        }

        $oldStatus = $jobCard->status;
        $jobCard->update(['status' => $status]);

        // SMS: notify on Completed or Broken via quick status
        if (in_array($status, ['Completed', 'Broken']) && $oldStatus !== $status) {
            try {
                (new SmsService())->sendTemplate('job_status_changed', $jobCard->phone_no, [
                    'customer_name' => $jobCard->customer_name,
                    'order_no'      => $jobCard->order_no,
                    'status'        => $status,
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[SMS] job_status_changed (quick) failed: ' . $e->getMessage());
            }
        }

        return response()->json(['ok' => true, 'status' => $status]);
    }

    public function payment(JobCard $jobCard)
    {
        $jobCard->load('invoiceItems');
        return response()->json([
            'id'             => $jobCard->id,
            'order_no'       => $jobCard->order_no,
            'customer_name'  => $jobCard->customer_name,
            'device_name'    => $jobCard->device_name,
            'grand_total'    => $jobCard->grand_total,
            'advance_amount' => (float) $jobCard->advance_amount,
            'paid_amount'    => (float) $jobCard->paid_amount,
            'balance'        => $jobCard->balance,
            'status'         => $jobCard->status,
            'payment_status' => $jobCard->payment_status,
        ]);
    }

    public function completePayment(Request $request, JobCard $jobCard)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'note'        => 'nullable|string|max:100',
        ]);

        $jobCard->load('invoiceItems');

        $amountNow   = (float) $request->amount_paid;
        $alreadyPaid = (float) $jobCard->paid_amount;
        $grandTotal  = $jobCard->grand_total;
        $totalPaid   = $alreadyPaid + $amountNow;
        $isFullyPaid = $totalPaid >= $grandTotal;

        // Log this individual payment
        \App\Models\PaymentLog::create([
            'job_card_id' => $jobCard->id,
            'amount'      => $amountNow,
            'note'        => $request->input('note') ?: ($alreadyPaid > 0 ? 'Instalment' : 'Payment'),
            'paid_at'     => now(),
        ]);

        if ($isFullyPaid) {
            $jobCard->update([
                'paid_amount'      => $totalPaid,
                'payment_status'   => 'paid',
                'payment_received' => 1,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'ok'          => true,
                    'type'        => 'full',
                    'message'     => "Order {$jobCard->order_no} fully paid.",
                    'receipt_url' => route('admin.jobcards.receipt', ['type' => 'jobcard', 'id' => $jobCard->id]),
                    'redirect'    => route('admin.jobcards.index'),
                ]);
            }

            return redirect()->route('admin.jobcards.index')
                             ->with('success', "Order {$jobCard->order_no} fully paid.");
        } else {
            $jobCard->update([
                'paid_amount'      => $totalPaid,
                'payment_status'   => 'partial',
                'payment_received' => 1,
            ]);

            $balance = $grandTotal - $totalPaid;

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'ok'          => true,
                    'type'        => 'partial',
                    'message'     => "Partial payment of Rs." . number_format($amountNow, 2) . " recorded. Balance: Rs." . number_format($balance, 2),
                    'receipt_url' => route('admin.jobcards.receipt', ['type' => 'jobcard', 'id' => $jobCard->id]),
                    'redirect'    => route('admin.jobcards.index'),
                ]);
            }

            return redirect()->route('admin.jobcards.index')
                             ->with('warning', "Order {$jobCard->order_no}: partial payment recorded. Balance: Rs." . number_format($balance, 2));
        }
    }

    public function receipt(string $type, int $id)
    {
        $shop = Shop::find(session('shop_id'));

        if ($type === 'delivered') {
            $order = DeliveredOrder::findOrFail($id);
            return view('admin.jobcards.receipt', [
                'shop'         => $shop,
                'orderNo'      => $order->order_no,
                'invoiceNo'    => $order->invoice_no,
                'customer'     => $order->customer_name,
                'phone'        => $order->phone_no,
                'address'      => $order->customer_address,
                'device'       => trim(($order->device_name ?? '') . ' ' . ($order->device_brand ?? '')),
                'serial'       => $order->serial_no,
                'fault'        => $order->device_fault,
                'receivedDate' => $order->date,
                'deliveredAt'  => $order->delivered_at,
                'serviceCharge'  => (float)$order->rupees,
                'advanceAmount'  => (float)($order->advance_amount ?? 0),
                'discount'       => (float)$order->discount,
                'grandTotal'     => (float)$order->grand_total,
                'paidAmount'     => (float)$order->paid_amount,
                'balance'        => 0,
                'paymentType'    => 'full',
                'invoiceItems'   => $order->invoice_items ?? [],
                'remark'         => $order->remark,
            ]);
        } else {
            $job = JobCard::with(['invoiceItems', 'paymentLogs'])->findOrFail($id);
            return view('admin.jobcards.receipt', [
                'shop'         => $shop,
                'orderNo'      => $job->order_no,
                'invoiceNo'    => $job->invoice_no,
                'customer'     => $job->customer_name,
                'phone'        => $job->phone_no,
                'address'      => $job->customer_address,
                'device'       => trim(($job->device_name ?? '') . ' ' . ($job->device_brand ?? '')),
                'serial'       => $job->serial_no,
                'fault'        => $job->device_fault,
                'receivedDate' => $job->date,
                'deliveredAt'  => null,
                'serviceCharge'  => (float)$job->rupees,
                'advanceAmount'  => (float)($job->advance_amount ?? 0),
                'discount'       => (float)$job->discount,
                'grandTotal'     => (float)$job->grand_total,
                'paidAmount'     => (float)$job->paid_amount,
                'balance'        => (float)$job->balance,
                'paymentType'    => $job->balance <= 0 ? 'full' : 'partial',
                'paymentLogs'    => $job->paymentLogs,
                'invoiceItems' => $job->invoiceItems->map(fn($i) => [
                    'description' => $i->description,
                    'quantity'    => $i->qty ?? 1,
                    'unit_price'  => $i->unit_price,
                    'total'       => $i->total,
                ])->toArray(),
                'remark'       => $job->remark,
            ]);
        }
    }
}
