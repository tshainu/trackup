<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvProject;
use App\Models\CctvLead;
use App\Models\CctvQuotation;
use App\Models\Employee;
use Illuminate\Http\Request;

class CctvProjectController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'all');
        $search = $request->get('q');
        $query  = CctvProject::latest();
        if ($tab !== 'all') {
            $stageMap = [
                'survey'    => 'Survey Complete',
                'materials' => 'Materials Ready',
                'started'   => 'Installation Started',
                'config'    => 'Configuration',
                'testing'   => 'Testing',
                'handover'  => 'Customer Handover',
                'warranty'  => 'Warranty Activated',
            ];
            if (isset($stageMap[$tab])) $query->where('stage', $stageMap[$tab]);
        }
        if ($search) {
            $query->where(fn($q) => $q->where('customer_name','like',"%$search%")
                ->orWhere('project_no','like',"%$search%"));
        }
        $projects = $query->paginate(20)->withQueryString();
        $counts = [
            'all'       => CctvProject::count(),
            'active'    => CctvProject::whereNotIn('stage',['Customer Handover','Warranty Activated'])->count(),
            'completed' => CctvProject::where('stage','Warranty Activated')->count(),
        ];
        return view('admin.cctv.projects.index', compact('projects','tab','search','counts'));
    }

    public function create(Request $request)
    {
        $employees  = Employee::where('status','active')->orderBy('employee_name')->get();
        $leads      = CctvLead::where('status','Approved')->orderBy('customer_name')->get();
        $quotations = CctvQuotation::where('status','Approved')->orderBy('customer_name')->get();
        $leadId     = $request->get('lead_id');
        $lead       = $leadId ? CctvLead::find($leadId) : null;
        $quotationId = $request->get('quotation_id');
        $quotation   = $quotationId ? CctvQuotation::with('lead')->find($quotationId) : null;
        // If address not on quotation, pull from the linked lead
        if ($quotation && !$lead && $quotation->lead_id) {
            $lead = $quotation->lead;
        }
        return view('admin.cctv.projects.create', compact('employees','leads','quotations','lead','quotation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'mobile'        => 'nullable|string|max:20',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
        ]);

        // Resolve technician names from selected IDs
        $techIds   = array_filter((array) $request->input('technician_ids', []));
        $techNames = [];
        if (!empty($techIds)) {
            $techNames = Employee::whereIn('id', $techIds)->pluck('employee_name')->toArray();
        }

        // Build equipment_list from submitted items[]
        $rawItems = $request->input('items', []);
        $equipmentList = [];
        foreach ($rawItems as $row) {
            if (empty($row['description'])) continue;
            $equipmentList[] = [
                'name'       => trim($row['description']),
                'qty'        => (int)($row['qty'] ?? 1),
                'unit_price' => (float)($row['unit_price'] ?? 0),
                'total'      => (int)($row['qty'] ?? 1) * (float)($row['unit_price'] ?? 0),
            ];
        }

        $project = CctvProject::create([
            'project_no'      => CctvProject::nextProjectNo(),
            'lead_id'         => $request->lead_id ?: null,
            'quotation_id'    => $request->quotation_id ?: null,
            'customer_id'     => $request->customer_id ?: null,
            'customer_name'   => $request->customer_name,
            'mobile'          => $request->mobile,
            'address'         => $request->address,
            'status'          => $request->input('status', 'scheduled'),
            'start_date'      => $request->start_date ?: null,
            'end_date'        => $request->end_date ?: null,
            'technician_ids'  => $techIds ?: null,
            'technician_name' => implode(', ', $techNames),
            'camera_count'    => (int) $request->input('camera_count', 0),
            'contract_amount' => (float) $request->input('contract_amount', 0),
            'advance_paid'    => (float) $request->input('advance_paid', 0),
            'scope'           => $request->scope,
            'equipment_list'  => !empty($equipmentList) ? $equipmentList : null,
            'stage'           => 'Survey Complete',
            'notes'           => $request->notes,
        ]);

        return redirect()->route('admin.cctv.projects.show', $project)
            ->with('success', "Project {$project->project_no} created.");
    }

    public function show(CctvProject $project)
    {
        $project->load(['assets','amcContracts']);
        $employees = Employee::where('status','active')->orderBy('employee_name')->get();
        return view('admin.cctv.projects.show', compact('project','employees'));
    }

    public function edit(CctvProject $project)
    {
        $employees = Employee::where('status','active')->orderBy('employee_name')->get();
        return view('admin.cctv.projects.edit', compact('project','employees'));
    }

    public function update(Request $request, CctvProject $project)
    {
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'stage'         => 'required|in:Survey Complete,Materials Ready,Installation Started,Configuration,Testing,Customer Handover,Warranty Activated',
        ]);

        $data = $request->only(['customer_name','mobile','address','installation_date','completion_date','team_assigned','stage','notes']);
        if ($request->stage === 'Warranty Activated' && !$project->completion_date) {
            $data['completion_date'] = now()->toDateString();
        }
        $project->update($data);
        return redirect()->route('admin.cctv.projects.show', $project)->with('success', 'Project updated.');
    }

    public function updateStage(Request $request, CctvProject $project)
    {
        $request->validate(['stage' => 'required|in:Survey Complete,Materials Ready,Installation Started,Configuration,Testing,Customer Handover,Warranty Activated']);
        $project->update(['stage' => $request->stage]);
        return back()->with('success', "Stage updated to: {$request->stage}");
    }

    public function destroy(CctvProject $project)
    {
        $no = $project->project_no;
        $project->delete();
        return redirect()->route('admin.cctv.projects.index')->with('success', "Project {$no} deleted.");
    }
}
