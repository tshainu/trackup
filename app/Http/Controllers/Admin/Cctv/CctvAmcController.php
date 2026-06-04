<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvAmcContract;
use App\Models\CctvAmcVisit;
use App\Models\CctvProject;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Http\Request;

class CctvAmcController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'all');
        $search = $request->get('q');
        $query  = CctvAmcContract::latest();

        if ($tab !== 'all') {
            $map = ['active'=>'Active','expired'=>'Expired','cancelled'=>'Cancelled','pending'=>'Pending'];
            if (isset($map[$tab])) $query->where('status', $map[$tab]);
        }
        if ($search) {
            $query->where(fn($q) => $q->where('customer_name','like',"%$search%")
                ->orWhere('amc_no','like',"%$search%"));
        }

        $contracts = $query->paginate(20)->withQueryString();
        $counts = [
            'all'      => CctvAmcContract::count(),
            'active'   => CctvAmcContract::where('status','Active')->count(),
            'expired'  => CctvAmcContract::where('status','Expired')->count(),
            'pending'  => CctvAmcContract::where('status','Pending')->count(),
        ];
        $renewalDue = CctvAmcContract::where('status','Active')
            ->whereDate('end_date','<=', now()->addDays(30))
            ->count();

        return view('admin.cctv.amc.index', compact('contracts','tab','search','counts','renewalDue'));
    }

    public function create(Request $request)
    {
        $projects = CctvProject::where('stage','Warranty Activated')->orderBy('customer_name')->get();
        return view('admin.cctv.amc.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'   => 'required|string|max:150',
            'mobile'          => 'nullable|string|max:20',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after:start_date',
            'contract_value'  => 'nullable|numeric|min:0',
            'visit_frequency' => 'required|in:Monthly,Quarterly,Half Yearly,Yearly',
        ]);

        $visitsMap = ['Monthly'=>12,'Quarterly'=>4,'Half Yearly'=>2,'Yearly'=>1];

        $contract = CctvAmcContract::create([
            'amc_no'          => CctvAmcContract::nextAmcNo(),
            'customer_id'     => $request->customer_id,
            'customer_name'   => $request->customer_name,
            'mobile'          => $request->mobile,
            'address'         => $request->address,
            'project_id'      => $request->project_id,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'contract_value'  => $request->contract_value ?? 0,
            'visit_frequency' => $request->visit_frequency,
            'visits_included' => $visitsMap[$request->visit_frequency] ?? 4,
            'visits_used'     => 0,
            'status'          => 'Active',
            'notes'           => $request->notes,
        ]);

        return redirect()->route('admin.cctv.amc.show', $contract)
            ->with('success', "AMC {$contract->amc_no} created.");
    }

    public function show(CctvAmcContract $amc)
    {
        $amc->load(['visits.technician','tickets']);
        $employees = Employee::where('status','active')->orderBy('employee_name')->get();
        return view('admin.cctv.amc.show', compact('amc','employees'));
    }

    public function edit(CctvAmcContract $amc)
    {
        $projects = CctvProject::orderBy('customer_name')->get();
        return view('admin.cctv.amc.edit', compact('amc','projects'));
    }

    public function update(Request $request, CctvAmcContract $amc)
    {
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date',
            'status'        => 'required|in:Active,Expired,Cancelled,Pending',
        ]);
        $amc->update($request->only(['customer_name','mobile','address','start_date','end_date','contract_value','visit_frequency','visits_included','status','notes']));
        return redirect()->route('admin.cctv.amc.show', $amc)->with('success', 'AMC updated.');
    }

    public function addVisit(Request $request, CctvAmcContract $amc)
    {
        $request->validate([
            'visit_date'   => 'required|date',
            'technician_id'=> 'nullable|exists:employees,id',
        ]);
        CctvAmcVisit::create([
            'amc_id'       => $amc->id,
            'visit_date'   => $request->visit_date,
            'technician_id'=> $request->technician_id,
            'notes'        => $request->notes,
            'status'       => $request->status ?? 'Scheduled',
        ]);
        if ($request->status === 'Completed') {
            $amc->increment('visits_used');
        }
        return back()->with('success', 'Visit added.');
    }

    public function destroy(CctvAmcContract $amc)
    {
        $no = $amc->amc_no;
        $amc->delete();
        return redirect()->route('admin.cctv.amc.index')->with('success', "AMC {$no} deleted.");
    }
}
