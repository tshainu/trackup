<?php
namespace App\Http\Controllers\Admin\Cctv;

use App\Http\Controllers\Controller;
use App\Models\CctvServiceTicket;
use App\Models\CctvAmcContract;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Http\Request;

class CctvServiceTicketController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'all');
        $search = $request->get('q');
        $query  = CctvServiceTicket::with('technician')->latest();

        if ($tab !== 'all') {
            $map = [
                'open'     => 'Open', 'assigned' => 'Assigned',
                'progress' => 'In Progress', 'parts' => 'Waiting Parts',
                'completed'=> 'Completed', 'closed'  => 'Closed',
            ];
            if (isset($map[$tab])) $query->where('status', $map[$tab]);
        }
        if ($search) {
            $query->where(fn($q) => $q->where('customer_name','like',"%$search%")
                ->orWhere('ticket_no','like',"%$search%")
                ->orWhere('mobile','like',"%$search%"));
        }

        $tickets = $query->paginate(20)->withQueryString();
        $counts = [
            'all'       => CctvServiceTicket::count(),
            'open'      => CctvServiceTicket::where('status','Open')->count(),
            'assigned'  => CctvServiceTicket::where('status','Assigned')->count(),
            'progress'  => CctvServiceTicket::where('status','In Progress')->count(),
            'parts'     => CctvServiceTicket::where('status','Waiting Parts')->count(),
            'completed' => CctvServiceTicket::where('status','Completed')->count(),
        ];
        return view('admin.cctv.service-tickets.index', compact('tickets','tab','search','counts'));
    }

    public function create(Request $request)
    {
        $employees = Employee::where('status','active')->orderBy('employee_name')->get();
        $amcs      = CctvAmcContract::where('status','Active')->orderBy('customer_name')->get();
        return view('admin.cctv.service-tickets.create', compact('employees','amcs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'     => 'required|string|max:150',
            'mobile'            => 'nullable|string|max:20',
            'ticket_type'       => 'required',
            'priority'          => 'required|in:Low,Normal,High,Urgent',
            'complaint_details' => 'nullable|string',
        ]);

        $ticket = CctvServiceTicket::create([
            'ticket_no'          => CctvServiceTicket::nextTicketNo(),
            'customer_id'        => $request->customer_id,
            'customer_name'      => $request->customer_name,
            'mobile'             => $request->mobile,
            'address'            => $request->address,
            'ticket_type'        => $request->ticket_type,
            'complaint_details'  => $request->complaint_details,
            'priority'           => $request->priority,
            'assigned_technician'=> $request->assigned_technician,
            'scheduled_date'     => $request->scheduled_date,
            'service_charge'     => $request->service_charge ?? 0,
            'ticket_source'      => $request->ticket_source ?? 'On-The-Go',
            'amc_id'             => $request->amc_id,
            'status'             => $request->assigned_technician ? 'Assigned' : 'Open',
        ]);

        return redirect()->route('admin.cctv.service-tickets.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_no} created.");
    }

    public function show(CctvServiceTicket $serviceTicket)
    {
        $serviceTicket->load('technician','amc');
        return view('admin.cctv.service-tickets.show', compact('serviceTicket'));
    }

    public function edit(CctvServiceTicket $serviceTicket)
    {
        $employees = Employee::where('status','active')->orderBy('employee_name')->get();
        $amcs      = CctvAmcContract::where('status','Active')->orderBy('customer_name')->get();
        return view('admin.cctv.service-tickets.edit', compact('serviceTicket','employees','amcs'));
    }

    public function update(Request $request, CctvServiceTicket $serviceTicket)
    {
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'status'        => 'required|in:Open,Assigned,In Progress,Waiting Parts,Completed,Closed',
        ]);
        $data = $request->only(['customer_name','mobile','address','ticket_type','complaint_details','priority','assigned_technician','scheduled_date','service_charge','paid_amount','resolution_notes','status']);
        if ($request->status === 'Completed' && !$serviceTicket->completed_at) {
            $data['completed_at'] = now();
        }
        $serviceTicket->update($data);
        return redirect()->route('admin.cctv.service-tickets.show', $serviceTicket)->with('success', 'Ticket updated.');
    }

    public function updateStatus(Request $request, CctvServiceTicket $serviceTicket)
    {
        $request->validate(['status' => 'required|in:Open,Assigned,In Progress,Waiting Parts,Completed,Closed']);
        $data = ['status' => $request->status];
        if ($request->status === 'Completed') $data['completed_at'] = now();
        $serviceTicket->update($data);
        return back()->with('success', "Status updated to {$request->status}.");
    }

    public function destroy(CctvServiceTicket $serviceTicket)
    {
        $no = $serviceTicket->ticket_no;
        $serviceTicket->delete();
        return redirect()->route('admin.cctv.service-tickets.index')->with('success', "Ticket {$no} deleted.");
    }
}
