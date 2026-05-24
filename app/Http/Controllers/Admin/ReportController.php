<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\Employee;
use App\Models\PaymentLog;
use App\Models\DeliveredOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // ── Period resolution ──────────────────────────────────────────
        $period = $request->input('period', 'month'); // today|week|month|year|custom
        $today  = Carbon::today();

        switch ($period) {
            case 'today':
                $from = $today->toDateString();
                $to   = $today->toDateString();
                break;
            case 'week':
                $from = $today->copy()->startOfWeek()->toDateString();
                $to   = $today->toDateString();
                break;
            case 'year':
                $from = $today->copy()->startOfYear()->toDateString();
                $to   = $today->toDateString();
                break;
            case 'custom':
                $from = $request->input('from', $today->copy()->startOfMonth()->toDateString());
                $to   = $request->input('to',   $today->toDateString());
                break;
            default: // month
                $period = 'month';
                $from = $today->copy()->startOfMonth()->toDateString();
                $to   = $today->toDateString();
        }

        $report = $request->input('report', 'jobs'); // jobs|payment|revenue|status|overdue|undelivered|staff

        // ── Base query ─────────────────────────────────────────────────
        $base = JobCard::with(['employee', 'invoiceItems', 'paymentLogs'])
            ->whereBetween('date', [$from, $to]);

        // ── 1. JOB REPORT ──────────────────────────────────────────────
        $jobs = (clone $base)->orderByDesc('date')->get();

        $jobSummary = [
            'total'          => $jobs->count(),
            'completed'      => $jobs->where('status', 'Completed')->count(),
            'in_progress'    => $jobs->where('status', 'In Progress')->count(),
            'pending'        => $jobs->where('status', 'Pending')->count(),
            'not_completed'  => $jobs->where('status', 'Not Completed')->count(),
            'broken'         => $jobs->where('status', 'Broken')->count(),
            'by_device'      => $jobs->groupBy('device_name')->map->count()->sortDesc()->take(8),
            'by_brand'       => $jobs->whereNotNull('device_brand')->groupBy('device_brand')->map->count()->sortDesc()->take(8),
        ];

        // ── 2. PAYMENT REPORT (job-wise) ────────────────────────────────
        $paymentJobs = (clone $base)->where(function($q){
            $q->where('paid_amount', '>', 0)->orWhere('payment_received', true);
        })->orderByDesc('date')->get();

        $paymentSummary = [
            'total_collected'  => $paymentJobs->sum('paid_amount'),
            'fully_paid_count' => $paymentJobs->where('payment_received', true)->count(),
            'partial_count'    => $paymentJobs->where('payment_received', false)->where('paid_amount', '>', 0)->count(),
            'transactions'     => $paymentJobs->count(),
        ];

        // Payment logs in period
        $paymentLogs = PaymentLog::with('jobCard')
            ->whereBetween('paid_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->orderByDesc('paid_at')
            ->get();

        // ── 3. REVENUE REPORT ───────────────────────────────────────────
        $revenueJobs = (clone $base)->orderByDesc('date')->get();

        $revenueSummary = [
            'grand_total_billed' => $revenueJobs->sum(fn($j) => $j->grand_total),
            'total_collected'    => $revenueJobs->sum('paid_amount'),
            'outstanding'        => $revenueJobs->sum(fn($j) => $j->balance),
            'fully_paid'         => $revenueJobs->where('payment_received', true)->sum('paid_amount'),
            'partial_paid'       => $revenueJobs->where('payment_received', false)->where('paid_amount', '>', 0)->sum('paid_amount'),
            'unpaid_count'       => $revenueJobs->where('payment_received', false)->where('paid_amount', 0)->count(),
        ];

        // ── 4. STATUS REPORT ────────────────────────────────────────────
        $statusJobs = (clone $base)->orderBy('status')->orderByDesc('date')->get();
        $statusGroups = $statusJobs->groupBy('status');

        // ── 5. DELIVERY OVERDUE ─────────────────────────────────────────
        $overdueJobs = JobCard::with('employee')
            ->whereNotNull('estimated_delivery')
            ->where('estimated_delivery', '<', $today)
            ->whereNotIn('status', ['Completed', 'Broken'])
            ->orderBy('estimated_delivery')
            ->get();

        // ── 6. UNDELIVERED (Completed but not archived, with/without balance) ──
        $undeliveredJobs = JobCard::with(['employee', 'invoiceItems'])
            ->where('status', 'Completed')
            ->orderByDesc('date')
            ->get();

        $undeliveredSummary = [
            'total'      => $undeliveredJobs->count(),
            'paid'       => $undeliveredJobs->where('payment_received', true)->count(),
            'outstanding'=> $undeliveredJobs->where('payment_received', false)->count(),
            'amount_due' => $undeliveredJobs->sum(fn($j) => $j->balance),
        ];

        // ── 7. STAFF REPORT ─────────────────────────────────────────────
        $employees  = Employee::all();
        $staffJobs  = (clone $base)->get();

        $staffData = $employees->map(function($emp) use ($staffJobs, $from, $to) {
            $myJobs = $staffJobs->where('employee_id', $emp->id);
            return [
                'employee'      => $emp,
                'total'         => $myJobs->count(),
                'completed'     => $myJobs->where('status', 'Completed')->count(),
                'in_progress'   => $myJobs->where('status', 'In Progress')->count(),
                'pending'       => $myJobs->where('status', 'Pending')->count(),
                'broken'        => $myJobs->where('status', 'Broken')->count(),
                'need_assist'   => $myJobs->where('need_assistant', true)->count(),
                'jobs'          => $myJobs->sortByDesc('date'),
            ];
        })->sortByDesc(fn($d) => $d['total']);

        // Unassigned jobs
        $unassignedJobs = $staffJobs->whereNull('employee_id');

        return view('admin.reports.index', compact(
            'report', 'period', 'from', 'to',
            'jobs', 'jobSummary',
            'paymentJobs', 'paymentSummary', 'paymentLogs',
            'revenueJobs', 'revenueSummary',
            'statusJobs', 'statusGroups',
            'overdueJobs',
            'undeliveredJobs', 'undeliveredSummary',
            'employees', 'staffData', 'unassignedJobs'
        ));
    }
}
