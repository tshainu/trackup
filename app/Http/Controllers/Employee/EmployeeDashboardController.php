<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\FieldComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $employeeId = Session::get('employee_id');
        $stats = [
            'assigned'       => JobCard::where('employee_id', $employeeId)->count(),
            'pending'        => JobCard::where('employee_id', $employeeId)->where('status', 'Pending')->count(),
            'in_progress'    => JobCard::where('employee_id', $employeeId)->where('status', 'In Progress')->count(),
            'completed'      => JobCard::where('employee_id', $employeeId)->where('status', 'Completed')->count(),
            'field_assigned' => FieldComplaint::where('assigned_to', $employeeId)->whereIn('status', ['Assigned','In Progress'])->count(),
        ];
        $myJobs       = JobCard::where('employee_id', $employeeId)->latest()->take(10)->get();
        $myFieldJobs  = FieldComplaint::where('assigned_to', $employeeId)
                            ->whereIn('status', ['Assigned','In Progress'])
                            ->latest()->take(5)->get();
        return view('employee.dashboard', compact('stats', 'myJobs', 'myFieldJobs'));
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

    public function acceptJob(JobCard $jobCard)
    {
        if ($jobCard->employee_id !== Session::get('employee_id')) abort(403);

        if ($jobCard->status === 'Pending') {
            $jobCard->update(['status' => 'In Progress']);
        }

        return redirect()->route('employee.jobs')
                         ->with('success', "Job {$jobCard->order_no} accepted — status set to In Progress.");
    }

    public function saveStatus(Request $request, JobCard $jobCard)
    {
        if ($jobCard->employee_id !== Session::get('employee_id')) abort(403);

        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed,Not Completed',
            'remark' => 'nullable|string|max:500',
        ]);

        $jobCard->update(['status' => $request->status, 'remark' => $request->remark]);
        return redirect()->route('employee.jobs')->with('success', 'Status updated.');
    }

    // ── Field Service Jobs ────────────────────────────────────────────────────

    public function fieldJobs()
    {
        $employeeId = Session::get('employee_id');
        $fieldJobs  = FieldComplaint::where('assigned_to', $employeeId)
                        ->orderByRaw("CASE status WHEN 'In Progress' THEN 0 WHEN 'Assigned' THEN 1 ELSE 2 END")
                        ->latest()->paginate(20);
        return view('employee.field-jobs', compact('fieldJobs'));
    }

    public function acceptFieldJob(Request $request, FieldComplaint $fieldComplaint)
    {
        if ($fieldComplaint->assigned_to !== Session::get('employee_id')) abort(403);

        // Only accept if currently Assigned
        if ($fieldComplaint->status === 'Assigned') {
            $fieldComplaint->update([
                'status' => 'In Progress',
            ]);
        }

        return redirect()->route('employee.field-jobs')
                         ->with('success', "Job {$fieldComplaint->complaint_no} accepted — status set to In Progress.");
    }

    public function completeFieldJobForm(FieldComplaint $fieldComplaint)
    {
        if ($fieldComplaint->assigned_to !== Session::get('employee_id')) abort(403);
        return view('employee.field-job-complete', compact('fieldComplaint'));
    }

    public function completeFieldJob(Request $request, FieldComplaint $fieldComplaint)
    {
        if ($fieldComplaint->assigned_to !== Session::get('employee_id')) abort(403);

        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        $fieldComplaint->update([
            'status'            => 'Completed',
            'completion_notes'  => $request->completion_notes,
            'completed_at'      => now(),
        ]);

        return redirect()->route('employee.field-jobs')
                         ->with('success', "Job {$fieldComplaint->complaint_no} marked as Completed.");
    }
}
