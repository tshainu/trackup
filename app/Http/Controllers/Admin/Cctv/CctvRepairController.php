<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvRepair;
use App\Models\Employee;
use Illuminate\Http\Request;

class CctvRepairController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'all');
        $search = $request->get('q');
        $query  = CctvRepair::with('technician')->latest();

        if ($tab !== 'all') {
            $map = [
                'received'   => 'Received', 'diagnosing' => 'Diagnosing',
                'repairing'  => 'Repairing', 'testing'   => 'Testing',
                'ready'      => 'Ready',     'collected'  => 'Collected',
            ];
            if (isset($map[$tab])) $query->where('status', $map[$tab]);
        }
        if ($search) {
            $query->where(fn($q) => $q->where('customer_name','like',"%$search%")
                ->orWhere('repair_no','like',"%$search%")
                ->orWhere('device_type','like',"%$search%"));
        }

        $repairs = $query->paginate(20)->withQueryString();
        $counts = [
            'all'       => CctvRepair::count(),
            'received'  => CctvRepair::where('status','Received')->count(),
            'repairing' => CctvRepair::whereIn('status',['Diagnosing','Repairing','Testing'])->count(),
            'ready'     => CctvRepair::where('status','Ready')->count(),
            'collected' => CctvRepair::where('status','Collected')->count(),
        ];
        return view('admin.cctv.repairs.index', compact('repairs','tab','search','counts'));
    }

    public function create()
    {
        $employees = Employee::where('status','active')->orderBy('employee_name')->get();
        return view('admin.cctv.repairs.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:150',
            'device_type'      => 'required|string|max:100',
            'fault_description'=> 'required|string',
            'technician_id'    => 'nullable|exists:employees,id',
        ]);

        $repair = CctvRepair::create([
            'repair_no'         => CctvRepair::nextRepairNo(),
            'customer_id'       => $request->customer_id,
            'customer_name'     => $request->customer_name,
            'mobile'            => $request->mobile,
            'device_type'       => $request->device_type,
            'brand'             => $request->brand,
            'model'             => $request->model,
            'serial_number'     => $request->serial_number,
            'fault_description' => $request->fault_description,
            'technician_id'     => $request->technician_id,
            'repair_cost'       => $request->repair_cost ?? 0,
            'received_date'     => $request->received_date ?? now()->toDateString(),
            'status'            => 'Received',
            'notes'             => $request->notes,
        ]);

        return redirect()->route('admin.cctv.repairs.show', $repair)
            ->with('success', "Repair {$repair->repair_no} created.");
    }

    public function show(CctvRepair $repair)
    {
        $repair->load('technician');
        return view('admin.cctv.repairs.show', compact('repair'));
    }

    public function edit(CctvRepair $repair)
    {
        $employees = Employee::where('status','active')->orderBy('employee_name')->get();
        return view('admin.cctv.repairs.edit', compact('repair','employees'));
    }

    public function update(Request $request, CctvRepair $repair)
    {
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'device_type'   => 'required|string|max:100',
            'status'        => 'required|in:Received,Diagnosing,Repairing,Testing,Ready,Collected,Cancelled',
        ]);

        $parts = [];
        if ($request->has('parts')) {
            foreach ($request->parts as $part) {
                if (!empty($part['name'])) {
                    $parts[] = [
                        'name' => $part['name'],
                        'qty'  => (int)($part['qty'] ?? 1),
                        'cost' => (float)($part['cost'] ?? 0),
                    ];
                }
            }
        }

        $data = $request->only(['customer_name','mobile','device_type','brand','model','serial_number','fault_description','technician_id','repair_notes','repair_cost','paid_amount','received_date','status','notes']);
        $data['parts_used'] = $parts;
        if ($request->status === 'Ready' && !$repair->completed_date) {
            $data['completed_date'] = now()->toDateString();
        }
        $repair->update($data);
        return redirect()->route('admin.cctv.repairs.show', $repair)->with('success', 'Repair updated.');
    }

    public function destroy(CctvRepair $repair)
    {
        $no = $repair->repair_no;
        $repair->delete();
        return redirect()->route('admin.cctv.repairs.index')->with('success', "Repair {$no} deleted.");
    }
}
