<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\InvoiceItem;
use App\Models\StoreInfo;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /** Invoice listing + search */
    public function index(Request $request)
    {
        $results    = collect();
        $query      = trim($request->input('q', ''));
        $filter     = $request->input('filter', 'all'); // all|paid|unpaid|partial

        if ($query !== '') {
            $results = JobCard::with(['employee', 'invoiceItems'])
                ->where(function ($q) use ($query) {
                    $q->where('order_no',       'like', "%$query%")
                      ->orWhere('invoice_no',   'like', "%$query%")
                      ->orWhere('customer_nic', 'like', "%$query%")
                      ->orWhere('phone_no',     'like', "%$query%")
                      ->orWhere('customer_name','like', "%$query%")
                      ->orWhere('device_name',  'like', "%$query%")
                      ->orWhere('device_brand', 'like', "%$query%");
                })
                ->orderByDesc('id')
                ->limit(50)
                ->get();
        }

        // All invoices with filter + pagination
        $allQuery = JobCard::with('invoiceItems')->orderByDesc('id');

        if ($filter === 'paid') {
            $allQuery->where('payment_received', true);
        } elseif ($filter === 'unpaid') {
            $allQuery->where('payment_received', false)->where(function($q){ $q->whereNull('paid_amount')->orWhere('paid_amount', 0); });
        } elseif ($filter === 'partial') {
            $allQuery->where('payment_received', false)->where('paid_amount', '>', 0);
        } else {
            // 'all' tab = exclude fully paid
            $allQuery->where('payment_received', false);
        }

        $allInvoices = $allQuery->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total'   => JobCard::where('payment_received', false)->count(), // "All" = unpaid+partial
            'paid'    => JobCard::where('payment_received', true)->count(),
            'unpaid'  => JobCard::where('payment_received', false)->where(function($q){ $q->whereNull('paid_amount')->orWhere('paid_amount',0); })->count(),
            'partial' => JobCard::where('payment_received', false)->where('paid_amount','>',0)->count(),
            'revenue' => JobCard::sum('paid_amount'),
        ];

        return view('admin.invoices.index', compact('results', 'query', 'allInvoices', 'filter', 'stats'));
    }

    /** Live search JSON endpoint */
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));

        if ($query === '') {
            return response()->json(['count' => 0, 'results' => []]);
        }

        $results = JobCard::with('invoiceItems')
            ->where(function ($q) use ($query) {
                $q->where('order_no',       'like', "%$query%")
                  ->orWhere('invoice_no',   'like', "%$query%")
                  ->orWhere('customer_nic', 'like', "%$query%")
                  ->orWhere('phone_no',     'like', "%$query%")
                  ->orWhere('customer_name','like', "%$query%")
                  ->orWhere('device_name',  'like', "%$query%")
                  ->orWhere('device_brand', 'like', "%$query%");
            })
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        return response()->json([
            'count'   => $results->count(),
            'results' => $results->map(fn($job) => [
                'order_no'      => $job->order_no,
                'invoice_no'    => $job->invoice_no,
                'customer_name' => $job->customer_name,
                'customer_nic'  => $job->customer_nic,
                'phone_no'      => $job->phone_no,
                'device_name'   => $job->device_name,
                'device_brand'  => $job->device_brand,
                'date'          => $job->date?->format('d M Y'),
                'status'        => $job->status,
                'grand_total'   => number_format($job->grand_total, 2),
                'balance'       => number_format($job->balance, 2),
                'pay_status'    => (float)$job->paid_amount >= $job->grand_total && $job->grand_total > 0
                                    ? 'paid'
                                    : ((float)$job->paid_amount > 0 ? 'partial' : 'unpaid'),
                'url'           => route('admin.invoices.show', $job),
            ]),
        ]);
    }

    /** Show / generate invoice for a job card */
    public function show(JobCard $jobCard)
    {
        $jobCard->load(['employee', 'invoiceItems', 'paymentLogs']);
        $store = StoreInfo::first();

        // Auto-generate invoice_no if not yet set
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
            'discount'   => 'nullable|numeric|min:0',
            'paid_amount'=> 'nullable|numeric|min:0',
            'rupees'     => 'nullable|numeric|min:0',
            'items'      => 'nullable|array',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        // Update base charge + amounts
        $jobCard->update([
            'rupees'      => $request->input('rupees', $jobCard->rupees),
            'discount'    => $request->input('discount', 0),
            'paid_amount' => $request->input('paid_amount', 0),
        ]);

        // Sync line items
        $jobCard->invoiceItems()->delete();
        if ($request->filled('items')) {
            foreach ($request->items as $item) {
                $total = round($item['qty'] * $item['unit_price'], 2);
                InvoiceItem::create([
                    'job_card_id' => $jobCard->id,
                    'description' => $item['description'],
                    'qty'         => $item['qty'],
                    'unit_price'  => $item['unit_price'],
                    'total'       => $total,
                ]);
            }
        }

        // Mark payment_received if fully paid
        if ($jobCard->fresh()->balance <= 0 && $jobCard->paid_amount > 0) {
            $jobCard->update(['payment_received' => true]);
        }

        return redirect()->route('admin.invoices.show', $jobCard)
                         ->with('success', 'Invoice saved successfully.');
    }

    /** Mark as fully paid */
    public function markPaid(JobCard $jobCard)
    {
        $jobCard->load('invoiceItems');
        $jobCard->update([
            'paid_amount'     => $jobCard->grand_total,
            'payment_received'=> true,
        ]);
        return back()->with('success', 'Invoice marked as paid.');
    }
}
