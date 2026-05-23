<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\InvoiceItem;
use App\Models\StoreInfo;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /** Search page */
    public function index(Request $request)
    {
        $results = collect();
        $query   = trim($request->input('q', ''));

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
                ->limit(30)
                ->get();
        }

        return view('admin.invoices.index', compact('results', 'query'));
    }

    /** Show / generate invoice for a job card */
    public function show(JobCard $jobCard)
    {
        $jobCard->load(['employee', 'invoiceItems']);
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
