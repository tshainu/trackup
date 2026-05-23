<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use Illuminate\Support\Facades\Session;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $employeeId = Session::get('employee_id');
        $stats = [
            'assigned'    => JobCard::where('employee_id', $employeeId)->count(),
            'pending'     => JobCard::where('employee_id', $employeeId)->where('status', 'Pending')->count(),
            'in_progress' => JobCard::where('employee_id', $employeeId)->where('status', 'In Progress')->count(),
            'completed'   => JobCard::where('employee_id', $employeeId)->where('status', 'Completed')->count(),
        ];
        $myJobs = JobCard::where('employee_id', $employeeId)->latest()->take(10)->get();
        return view('employee.dashboard', compact('stats', 'myJobs'));
    }

    public function myJobs()
    {
        $employeeId = Session::get('employee_id');
        $jobs = JobCard::where('employee_id', $employeeId)->latest()->paginate(20);
        return view('employee.jobs', compact('jobs'));
    }

    public function updateStatus(JobCard $jobCard)
    {
        // Only the assigned employee can update
        if ($jobCard->employee_id !== Session::get('employee_id')) {
            abort(403);
        }
        return view('employee.update-status', compact('jobCard'));
    }

    public function saveStatus(\Illuminate\Http\Request $request, JobCard $jobCard)
    {
        if ($jobCard->employee_id !== Session::get('employee_id')) abort(403);

        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed,Not Completed',
            'remark' => 'nullable|string|max:500',
        ]);

        $jobCard->update(['status' => $request->status, 'remark' => $request->remark]);
        return redirect()->route('employee.jobs')->with('success', 'Status updated.');
    }
}
