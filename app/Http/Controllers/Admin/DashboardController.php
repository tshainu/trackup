<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\Employee;
use App\Models\StoreInfo;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'       => JobCard::count(),
            'pending'     => JobCard::where('status', 'Pending')->count(),
            'in_progress' => JobCard::where('status', 'In Progress')->count(),
            'completed'   => JobCard::where('status', 'Completed')->count(),
            'not_completed' => JobCard::where('status', 'Not Completed')->count(),
            'employees'   => Employee::where('status', 'active')->count(),
            'revenue'     => JobCard::where('status', 'Completed')->sum('rupees'),
            'today'       => JobCard::whereDate('created_at', today())->count(),
            'uncollected' => JobCard::where('status', 'Completed')->where('payment_received', 0)->count(),
        ];

        $recentJobs = JobCard::with('employee')->latest()->take(10)->get();
        $monthlyData = $this->getMonthlyData();
        $chartData = $this->getLast7DaysData();
        $store = StoreInfo::current();

        // Today's delivery list — jobs created or updated today (any status)
        $todayDeliveries = JobCard::with('employee')
            ->where(function($q) {
                $q->whereDate('created_at', today())
                  ->orWhereDate('updated_at', today());
            })
            ->latest('updated_at')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentJobs', 'monthlyData', 'chartData', 'store', 'todayDeliveries'));
    }

    private function getLast7DaysData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $data[] = [
                'total'     => JobCard::whereDate('created_at', $date)->count(),
                'completed' => JobCard::where('status', 'Completed')->whereDate('created_at', $date)->count(),
                'pending'   => JobCard::where('status', 'Pending')->whereDate('created_at', $date)->count(),
                'revenue'   => (float) JobCard::where('status', 'Completed')->whereDate('created_at', $date)->sum('rupees'),
            ];
        }
        return $data;
    }

    private function getMonthlyData(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = [
                'month'     => $date->format('M'),
                'completed' => JobCard::where('status', 'Completed')
                    ->whereYear('date', $date->year)
                    ->whereMonth('date', $date->month)
                    ->count(),
                'new'       => JobCard::whereYear('date', $date->year)
                    ->whereMonth('date', $date->month)
                    ->count(),
            ];
        }
        return $months;
    }
}
