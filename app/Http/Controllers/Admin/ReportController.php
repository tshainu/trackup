<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\Employee;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $jobs = JobCard::with('employee')
            ->whereBetween('date', [$from, $to])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
            ->latest('date')
            ->get();

        $summary = [
            'total'     => $jobs->count(),
            'completed' => $jobs->where('status', 'Completed')->count(),
            'pending'   => $jobs->where('status', 'Pending')->count(),
            'revenue'   => $jobs->where('status', 'Completed')->sum('rupees'),
        ];

        $byDevice = $jobs->groupBy('device_name')->map->count();
        $byStatus = $jobs->groupBy('status')->map->count();
        $employees = Employee::all();

        return view('admin.reports.index', compact('jobs','summary','byDevice','byStatus','employees','from','to'));
    }
}
