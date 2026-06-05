<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvLead;
use App\Models\Customer;
use Illuminate\Http\Request;

class CctvLeadController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'all');
        $search = $request->get('q');
        $query  = CctvLead::latest();

        if ($tab !== 'all') {
            $map = [
                'new'       => 'New Lead',
                'survey'    => 'Survey Scheduled',
                'surveyed'  => 'Survey Completed',
                'quoted'    => 'Quotation Sent',
                'approved'  => 'Approved',
                'lost'      => 'Lost',
            ];
            if (isset($map[$tab])) $query->where('status', $map[$tab]);
        }
        if ($search) {
            $query->where(fn($q) => $q->where('customer_name','like',"%$search%")
                ->orWhere('mobile','like',"%$search%")
                ->orWhere('lead_no','like',"%$search%"));
        }

        $leads  = $query->paginate(20)->withQueryString();
        $counts = [
            'all'      => CctvLead::count(),
            'new'      => CctvLead::where('status','New Lead')->count(),
            'survey'   => CctvLead::where('status','Survey Scheduled')->count(),
            'surveyed' => CctvLead::where('status','Survey Completed')->count(),
            'quoted'   => CctvLead::where('status','Quotation Sent')->count(),
            'approved' => CctvLead::where('status','Approved')->count(),
            'lost'     => CctvLead::where('status','Lost')->count(),
        ];

        return view('admin.cctv.leads.index', compact('leads','tab','search','counts'));
    }

    public function create()
    {
        return view('admin.cctv.leads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'      => 'required|string|max:150',
            'mobile'             => 'required|string|max:20',
            'address'            => 'nullable|string',
            'customer_type'      => 'required|in:Residential,Commercial,Government',
            'inquiry_date'       => 'nullable|date',
            'inquiry_source'     => 'nullable|string|max:100',
            'requirement_notes'  => 'nullable|string',
        ]);

        // Upsert customer
        $customer = Customer::where('phone', $request->mobile)->first()
            ?? Customer::create([
                'customer_id' => Customer::nextCustomerId(),
                'name'  => $request->customer_name,
                'phone' => $request->mobile,
                'address' => $request->address,
            ]);

        $lead = CctvLead::create([
            'lead_no'           => CctvLead::nextLeadNo(),
            'customer_id'       => $customer->id,
            'customer_name'     => $request->customer_name,
            'mobile'            => $request->mobile,
            'address'           => $request->address,
            'customer_type'     => $request->customer_type,
            'inquiry_date'      => $request->inquiry_date ?? now()->toDateString(),
            'inquiry_source'    => $request->inquiry_source,
            'requirement_notes' => $request->requirement_notes,
            'status'            => 'New Lead',
        ]);

        return redirect()->route('admin.cctv.leads.show', $lead)
            ->with('success', "Lead {$lead->lead_no} created.");
    }

    public function show(CctvLead $lead)
    {
        $lead->load(['surveys','quotations']);
        $survey    = $lead->surveys->first();
        $quotation = $lead->quotations->first();
        $project   = $quotation ? \App\Models\CctvProject::where('quotation_id',$quotation->id)->first()
                                : \App\Models\CctvProject::where('lead_id',$lead->id)->first();
        $invoice   = $project ? $project->invoice : null;
        return view('admin.cctv.leads.show', compact('lead','survey','quotation','project','invoice'));
    }

    public function edit(CctvLead $lead)
    {
        return view('admin.cctv.leads.edit', compact('lead'));
    }

    public function update(Request $request, CctvLead $lead)
    {
        $request->validate([
            'customer_name'     => 'required|string|max:150',
            'mobile'            => 'required|string|max:20',
            'customer_type'     => 'required|in:Residential,Commercial,Government',
            'status'            => 'required|in:New Lead,Survey Scheduled,Survey Completed,Quotation Sent,Approved,Lost',
        ]);

        $lead->update($request->only(['customer_name','mobile','address','customer_type','inquiry_date','inquiry_source','requirement_notes','status']));
        return redirect()->route('admin.cctv.leads.show', $lead)->with('success', 'Lead updated.');
    }

    public function destroy(CctvLead $lead)
    {
        $no = $lead->lead_no;
        $lead->delete();
        return redirect()->route('admin.cctv.leads.index')->with('success', "Lead {$no} deleted.");
    }
}
