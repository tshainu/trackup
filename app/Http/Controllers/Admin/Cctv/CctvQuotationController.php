<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvQuotation;
use App\Models\CctvLead;
use App\Models\StoreInfo;
use Illuminate\Http\Request;

class CctvQuotationController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'all');
        $search = $request->get('q');
        $query  = CctvQuotation::latest();
        if ($tab !== 'all') {
            $map = ['draft'=>'Draft','sent'=>'Sent','approved'=>'Approved','rejected'=>'Rejected'];
            if (isset($map[$tab])) $query->where('status', $map[$tab]);
        }
        if ($search) {
            $query->where(fn($q) => $q->where('customer_name','like',"%$search%")
                ->orWhere('quote_no','like',"%$search%"));
        }
        $quotations = $query->paginate(20)->withQueryString();
        $counts = [
            'all'      => CctvQuotation::count(),
            'draft'    => CctvQuotation::where('status','Draft')->count(),
            'sent'     => CctvQuotation::where('status','Sent')->count(),
            'approved' => CctvQuotation::where('status','Approved')->count(),
            'rejected' => CctvQuotation::where('status','Rejected')->count(),
        ];
        return view('admin.cctv.quotations.index', compact('quotations','tab','search','counts'));
    }

    public function create(Request $request)
    {
        $leads  = CctvLead::whereIn('status',['Survey Completed','Quotation Sent'])->orderBy('customer_name')->get();
        $leadId = $request->get('lead_id');
        $lead   = $leadId ? CctvLead::find($leadId) : null;
        return view('admin.cctv.quotations.create', compact('leads','lead'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'     => 'required|string|max:150',
            'mobile'            => 'nullable|string|max:20',
            'labour_cost'       => 'nullable|numeric|min:0',
            'installation_cost' => 'nullable|numeric|min:0',
            'transport_cost'    => 'nullable|numeric|min:0',
            'discount'          => 'nullable|numeric|min:0',
            'tax'               => 'nullable|numeric|min:0',
            'valid_until'       => 'nullable|date',
        ]);

        $equipList = [];
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                // form posts 'description', legacy/API may post 'name'
                $desc = trim($item['description'] ?? $item['name'] ?? '');
                if ($desc !== '') {
                    $qty   = (int)($item['qty'] ?? 1);
                    $price = (float)($item['unit_price'] ?? 0);
                    $equipList[] = [
                        'name'       => $desc,
                        'qty'        => $qty,
                        'unit_price' => $price,
                        'total'      => $qty * $price,
                    ];
                }
            }
        }

        // Field name aliases: forms post discount_amount / installation_charge
        $discount     = (float)($request->discount_amount    ?? $request->discount            ?? 0);
        $installCost  = (float)($request->installation_charge ?? $request->installation_cost  ?? 0);
        $labourCost   = (float)($request->labour_cost   ?? 0);
        $transportCost= (float)($request->transport_cost ?? 0);
        // Merge terms into notes if notes blank
        $notes = $request->notes ?? ($request->terms ?? null);

        $quot = new CctvQuotation();
        $quot->fill([
            'quote_no'          => CctvQuotation::nextQuoteNo(),
            'lead_id'           => $request->lead_id ?: null,
            'customer_id'       => $request->customer_id ?: null,
            'customer_name'     => $request->customer_name,
            'mobile'            => $request->mobile,
            'equipment_list'    => $equipList,
            'labour_cost'       => $labourCost,
            'installation_cost' => $installCost,
            'transport_cost'    => $transportCost,
            'discount'          => $discount,
            'tax'               => (float)($request->tax ?? 0),
            'status'            => $request->status ?? 'Draft',
            'valid_until'       => $request->valid_until,
            'notes'             => $notes,
        ]);
        $quot->grand_total = $quot->computeTotal();
        $quot->save();

        if ($request->lead_id) {
            CctvLead::find($request->lead_id)?->update(['status' => 'Quotation Sent']);
        }

        return redirect()->route('admin.cctv.quotations.show', $quot)
            ->with('success', "Quotation {$quot->quote_no} created.");
    }

    public function show(CctvQuotation $quotation)
    {
        $store = StoreInfo::current();
        return view('admin.cctv.quotations.show', compact('quotation','store'));
    }

    public function edit(CctvQuotation $quotation)
    {
        $leads = CctvLead::orderBy('customer_name')->get();
        return view('admin.cctv.quotations.edit', compact('quotation','leads'));
    }

    public function update(Request $request, CctvQuotation $quotation)
    {
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'status'        => 'required|in:Draft,Sent,Approved,Rejected',
        ]);

        $equipList = [];
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                $desc = trim($item['description'] ?? $item['name'] ?? '');
                if ($desc !== '') {
                    $qty   = (int)($item['qty'] ?? 1);
                    $price = (float)($item['unit_price'] ?? 0);
                    $equipList[] = [
                        'name'       => $desc,
                        'qty'        => $qty,
                        'unit_price' => $price,
                        'total'      => $qty * $price,
                    ];
                }
            }
        }

        $discount     = (float)($request->discount_amount    ?? $request->discount            ?? 0);
        $installCost  = (float)($request->installation_charge ?? $request->installation_cost  ?? 0);
        $labourCost   = (float)($request->labour_cost   ?? 0);
        $transportCost= (float)($request->transport_cost ?? 0);
        $notes = $request->notes ?? ($request->terms ?? null);

        $quotation->fill([
            'customer_name'     => $request->customer_name,
            'mobile'            => $request->mobile,
            'equipment_list'    => $equipList,
            'labour_cost'       => $labourCost,
            'installation_cost' => $installCost,
            'transport_cost'    => $transportCost,
            'discount'          => $discount,
            'tax'               => (float)($request->tax ?? 0),
            'status'            => $request->status,
            'valid_until'       => $request->valid_until,
            'notes'             => $notes,
        ]);
        $quotation->grand_total = $quotation->computeTotal();
        $quotation->save();

        if ($request->status === 'Approved' && $quotation->lead_id) {
            CctvLead::find($quotation->lead_id)?->update(['status' => 'Approved']);
        }

        return redirect()->route('admin.cctv.quotations.show', $quotation)->with('success', 'Quotation updated.');
    }

    public function pdf(CctvQuotation $quotation)
    {
        $store = StoreInfo::current();
        return view('admin.cctv.quotations.pdf', compact('quotation','store'));
    }

    public function destroy(CctvQuotation $quotation)
    {
        $no = $quotation->quote_no;
        $quotation->delete();
        return redirect()->route('admin.cctv.quotations.index')->with('success', "Quotation {$no} deleted.");
    }
}
