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
        return view('admin.cctv.projects.create', compact('employees','leads','quotations','lead'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'     => 'required|string|max:150',
            'mobile'            => 'nullable|string|max:20',
            'installation_date' => 'nullable|date',
        ]);

        $project = CctvProject::create([
            'project_no'       => CctvProject::nextProjectNo(),
            'lead_id'          => $request->lead_id,
            'quotation_id'     => $request->quotation_id,
            'customer_id'      => $request->customer_id,
            'customer_name'    => $request->customer_name,
            'mobile'           => $request->mobile,
            'address'          => $request->address,
            'installation_date'=> $request->installation_date,
            'team_assigned'    => $request->team_assigned ?? [],
            'stage'            => 'Survey Complete',
            'notes'            => $request->notes,
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
