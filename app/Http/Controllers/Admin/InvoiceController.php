<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\FieldComplaint;
use App\Models\InvoiceItem;
use App\Models\StoreInfo;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /** Invoice listing + search */
    public function index(Request $request)
    {
        $results = collect();
        $query   = trim($request->input('q', ''));
        $filter  = $request->input('filter', 'all'); // all|paid|unpaid|partial
        $type    = $request->input('type', 'all');   // all|job|field

        if ($query !== '') {
            $jobResults = JobCard::with(['employee', 'invoiceItems'])
                ->where(function ($q) use ($query) {
                    $q->where('order_no',       'like', "%$query%")
                      ->orWhere('invoice_no',   'like', "%$query%")
                      ->orWhere('customer_nic', 'like', "%$query%")
                      ->orWhere('phone_no',     'like', "%$query%")
                      ->orWhere('customer_name','like', "%$query%")
                      ->orWhere('device_name',  'like', "%$query%")
                      ->orWhere('device_brand', 'like', "%$query%");
                })
                ->orderByDesc('id')->limit(50)->get()
                ->map(fn($j) => $this->normalizeJob($j));

            $fieldResults = FieldComplaint::where(function ($q) use ($query) {
                    $q->where('complaint_no',   'like', "%$query%")
                      ->orWhere('invoice_no',   'like', "%$query%")
                      ->orWhere('phone_no',     'like', "%$query%")
                      ->orWhere('customer_name','like', "%$query%");
                })
                ->orderByDesc('id')->limit(50)->get()
                ->map(fn($f) => $this->normalizeField($f));

            $results = $jobResults->merge($fieldResults)->sortByDesc('id');
        }

        // ── Job invoices ──
        $jobQ = JobCard::with('invoiceItems')->orderByDesc('id');
        $fieldQ = FieldComplaint::orderByDesc('id');

        if ($filter === 'paid') {
            $jobQ->where('payment_received', true);
            $fieldQ->where('payment_received', true);
        } elseif ($filter === 'unpaid') {
            $jobQ->where('payment_received', false)->where(fn($q) => $q->whereNull('paid_amount')->orWhere('paid_amount', 0));
            $fieldQ->where('payment_received', false)->where(fn($q) => $q->whereNull('paid_amount')->orWhere('paid_amount', 0));
        } elseif ($filter === 'partial') {
            $jobQ->where('payment_received', false)->where('paid_amount', '>', 0);
            $fieldQ->where('payment_received', false)->where('paid_amount', '>', 0);
        } else {
            $jobQ->where('payment_received', false);
            $fieldQ->where('payment_received', false);
        }

        // Build merged paginated list
        $jobs   = ($type === 'field') ? collect() : $jobQ->get()->map(fn($j) => $this->normalizeJob($j));
        $fields = ($type === 'job')   ? collect() : $fieldQ->get()->map(fn($f) => $this->normalizeField($f));

        $merged = $jobs->merge($fields)->sortByDesc('raw_id');
        $page   = $request->input('page', 1);
        $perPage = 20;
        $allInvoices = new \Illuminate\Pagination\LengthAwarePaginator(
            $merged->forPage($page, $perPage)->values(),
            $merged->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Stats
        $stats = [
            'total'   => JobCard::where('payment_received', false)->count()
                        + FieldComplaint::where('payment_received', false)->count(),
            'paid'    => JobCard::where('payment_received', true)->count()
                        + FieldComplaint::where('payment_received', true)->count(),
            'unpaid'  => JobCard::where('payment_received', false)->where(fn($q) => $q->whereNull('paid_amount')->orWhere('paid_amount',0))->count()
                        + FieldComplaint::where('payment_received', false)->where(fn($q) => $q->whereNull('paid_amount')->orWhere('paid_amount',0))->count(),
            'partial' => JobCard::where('payment_received', false)->where('paid_amount','>',0)->count()
                        + FieldComplaint::where('payment_received', false)->where('paid_amount','>',0)->count(),
            'revenue' => JobCard::sum('paid_amount') + FieldComplaint::sum('paid_amount'),
        ];

        return view('admin.invoices.index', compact('results', 'query', 'allInvoices', 'filter', 'type', 'stats'));
    }

    /** Normalize JobCard to common shape */
    private function normalizeJob(JobCard $j): array
    {
        $paid = (float)$j->paid_amount;
        $gt   = (float)$j->grand_total;
        return [
            'type'          => 'job',
            'raw_id'        => $j->id,
            'order_no'      => $j->order_no,
            'invoice_no'    => $j->invoice_no,
            'customer_name' => $j->customer_name,
            'customer_nic'  => $j->customer_nic ?? '',
            'phone_no'      => $j->phone_no,
            'device'        => trim(($j->device_name ?? '') . ' ' . ($j->device_brand ?? '')),
            'date'          => $j->date?->format('d M Y'),
            'status'        => $j->status,
            'grand_total'   => $gt,
            'paid_amount'   => $paid,
            'balance'       => max(0, $gt - $paid),
            'pay_status'    => $j->payment_received ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
            'url'           => route('admin.invoices.show', $j->id),
        ];
    }

    /** Normalize FieldComplaint to common shape */
    private function normalizeField(FieldComplaint $f): array
    {
        $paid = (float)$f->paid_amount;
        $gt   = (float)$f->grand_total;
        return [
            'type'          => 'field',
            'raw_id'        => $f->id,
            'order_no'      => $f->complaint_no,
            'invoice_no'    => $f->invoice_no,
            'customer_name' => $f->customer_name,
            'customer_nic'  => '',
            'phone_no'      => $f->phone_no,
            'device'        => $f->service_type_name ?? 'Field Service',
            'date'          => $f->scheduled_date?->format('d M Y'),
            'status'        => $f->status,
            'grand_total'   => $gt,
            'paid_amount'   => $paid,
            'balance'       => max(0, $gt - $paid),
            'pay_status'    => $f->payment_received ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
            'url'           => route('admin.field-complaints.invoice', $f->id),
        ];
    }

    /** Live search JSON endpoint */
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        if ($query === '') return response()->json(['count' => 0, 'results' => []]);

        $jobs = JobCard::with('invoiceItems')
            ->where(fn($q) => $q->where('order_no','like',"%$query%")->orWhere('invoice_no','like',"%$query%")
                ->orWhere('customer_nic','like',"%$query%")->orWhere('phone_no','like',"%$query%")
                ->orWhere('customer_name','like',"%$query%")->orWhere('device_name','like',"%$query%")
                ->orWhere('device_brand','like',"%$query%"))
            ->orderByDesc('id')->limit(20)->get()
            ->map(fn($j) => array_merge($this->normalizeJob($j), [
                'grand_total' => number_format($j->grand_total,2),
                'balance'     => number_format(max(0,$j->grand_total-$j->paid_amount),2),
            ]));

        $fields = FieldComplaint::where(fn($q) => $q->where('complaint_no','like',"%$query%")
                ->orWhere('invoice_no','like',"%$query%")->orWhere('phone_no','like',"%$query%")
                ->orWhere('customer_name','like',"%$query%"))
            ->orderByDesc('id')->limit(20)->get()
            ->map(fn($f) => array_merge($this->normalizeField($f), [
                'grand_total' => number_format($f->grand_total,2),
                'balance'     => number_format(max(0,$f->grand_total-$f->paid_amount),2),
            ]));

        $results = $jobs->merge($fields)->sortByDesc('raw_id')->values();

        return response()->json(['count' => $results->count(), 'results' => $results]);
    }

    /** Show / generate invoice for a job card */
    public function show(JobCard $jobCard)
    {
        $jobCard->load(['employee', 'invoiceItems', 'paymentLogs']);
        $store = StoreInfo::current();

        if (!$jobCard->invoice_no) {
            $jobCard->update([
                'invoice_no'   => JobCard::nextInvoiceNo(),
                'invoice_date' => now()->toDateString(),
            ]);
        }

        return view('admin.invoices.show', compact('jobCard', 'store'));
    }

    /** Save line items + amounts */
    public function update(Request $request, JobCard $jobCard)
    {
        $request->validate([
            'discount' => 'nullable|numeric|min:0',
            'rupees'   => 'nullable|numeric|min:0',
            'items'    => 'nullable|array',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $jobCard->update([
            'rupees'   => $request->input('rupees', $jobCard->rupees),
            'discount' => $request->input('discount', 0),
        ]);

        $jobCard->invoiceItems()->delete();
        if ($request->filled('items')) {
            foreach ($request->items as $item) {
                InvoiceItem::create([
                    'job_card_id' => $jobCard->id,
                    'description' => $item['description'],
                    'qty'         => $item['qty'],
                    'unit_price'  => $item['unit_price'],
                    'total'       => round($item['qty'] * $item['unit_price'], 2),
                ]);
            }
        }

        if ($jobCard->fresh()->balance <= 0 && $jobCard->paid_amount > 0) {
            $jobCard->update(['payment_received' => true]);
        }

        return redirect()->route('admin.invoices.show', $jobCard)->with('success', 'Invoice saved successfully.');
    }

    /** Mark as fully paid */
    public function markPaid(JobCard $jobCard)
    {
        $jobCard->load('invoiceItems');
        $jobCard->update(['paid_amount' => $jobCard->grand_total, 'payment_received' => true]);
        return back()->with('success', 'Invoice marked as paid.');
    }
}
