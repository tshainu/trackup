<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvInvoice;
use App\Models\CctvProject;
use App\Models\CctvQuotation;
use App\Models\StoreInfo;
use Illuminate\Http\Request;

class CctvInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'all');
        $search = $request->get('q');
        $query  = CctvInvoice::with('project')->latest();

        if ($tab !== 'all') {
            $map = ['unpaid'=>'Unpaid','partial'=>'Partial','paid'=>'Paid'];
            if (isset($map[$tab])) $query->where('status', $map[$tab]);
        }
        if ($search) {
            $query->where(fn($q) => $q->where('customer_name','like',"%$search%")
                ->orWhere('invoice_no','like',"%$search%"));
        }

        $invoices = $query->paginate(20)->withQueryString();
        $counts = [
            'all'     => CctvInvoice::count(),
            'unpaid'  => CctvInvoice::where('status','Unpaid')->count(),
            'partial' => CctvInvoice::where('status','Partial')->count(),
            'paid'    => CctvInvoice::where('status','Paid')->count(),
        ];
        return view('admin.cctv.invoices.index', compact('invoices','tab','search','counts'));
    }

    public function create(Request $request)
    {
        $projectId = $request->get('project_id');
        $project   = $projectId ? CctvProject::with('quotation','lead')->find($projectId) : null;
        return view('admin.cctv.invoices.create', compact('project'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'mobile'        => 'nullable|string|max:20',
            'invoice_date'  => 'nullable|date',
            'due_date'      => 'nullable|date',
        ]);

        $rawItems = $request->input('items', []);
        $equipmentList = [];
        foreach ($rawItems as $row) {
            $desc = trim($row['description'] ?? '');
            if ($desc === '') continue;
            $qty   = (int)($row['qty'] ?? 1);
            $price = (float)($row['unit_price'] ?? 0);
            $equipmentList[] = [
                'name'       => $desc,
                'qty'        => $qty,
                'unit_price' => $price,
                'total'      => $qty * $price,
            ];
        }

        $labour   = (float)$request->input('labour_cost', 0);
        $install  = (float)$request->input('installation_cost', 0);
        $trans    = (float)$request->input('transport_cost', 0);
        $discount = (float)$request->input('discount', 0);
        $tax      = (float)$request->input('tax', 0);

        $itemsTotal = collect($equipmentList)->sum('total');
        $grandTotal = max(0, $itemsTotal + $labour + $install + $trans - $discount + $tax);

        $paidAmount = (float)$request->input('paid_amount', 0);
        $status = 'Unpaid';
        if ($paidAmount >= $grandTotal && $grandTotal > 0) $status = 'Paid';
        elseif ($paidAmount > 0) $status = 'Partial';

        $invoice = CctvInvoice::create([
            'invoice_no'        => CctvInvoice::nextInvoiceNo(),
            'project_id'        => $request->project_id ?: null,
            'quotation_id'      => $request->quotation_id ?: null,
            'lead_id'           => $request->lead_id ?: null,
            'customer_id'       => $request->customer_id ?: null,
            'customer_name'     => $request->customer_name,
            'mobile'            => $request->mobile,
            'address'           => $request->address,
            'equipment_list'    => !empty($equipmentList) ? $equipmentList : null,
            'labour_cost'       => $labour,
            'installation_cost' => $install,
            'transport_cost'    => $trans,
            'discount'          => $discount,
            'tax'               => $tax,
            'grand_total'       => $grandTotal,
            'paid_amount'       => $paidAmount,
            'status'            => $status,
            'invoice_date'      => $request->invoice_date ?? now()->toDateString(),
            'due_date'          => $request->due_date,
            'notes'             => $request->notes,
        ]);

        // Link invoice back to project + update project status
        if ($request->project_id) {
            $project = CctvProject::find($request->project_id);
            if ($project) {
                $project->update(['invoice_id' => $invoice->id, 'status' => 'completed']);
                // Update lead status to Completed
                if ($project->lead_id) {
                    \App\Models\CctvLead::find($project->lead_id)?->update(['status' => 'Completed']);
                }
            }
        }

        return redirect()->route('admin.cctv.invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_no} created.");
    }

    public function show(CctvInvoice $invoice)
    {
        $invoice->load('project','quotation','lead');
        $store = StoreInfo::first();
        return view('admin.cctv.invoices.show', compact('invoice','store'));
    }

    public function pdf(CctvInvoice $invoice)
    {
        $invoice->load('project','quotation','lead');
        $store = StoreInfo::first();
        return view('admin.cctv.invoices.pdf', compact('invoice','store'));
    }

    public function updatePayment(Request $request, CctvInvoice $invoice)
    {
        $request->validate(['paid_amount' => 'required|numeric|min:0']);
        $paid = (float)$request->paid_amount;
        $status = 'Unpaid';
        if ($paid >= $invoice->grand_total && $invoice->grand_total > 0) $status = 'Paid';
        elseif ($paid > 0) $status = 'Partial';
        $invoice->update(['paid_amount' => $paid, 'status' => $status]);
        return back()->with('success', 'Payment updated.');
    }

    public function destroy(CctvInvoice $invoice)
    {
        $no = $invoice->invoice_no;
        $invoice->delete();
        return redirect()->route('admin.cctv.invoices.index')->with('success', "Invoice {$no} deleted.");
    }
}
