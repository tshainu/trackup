<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\JobCard;
use App\Models\StoreInfo;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $today = Carbon::today()->toDateString();

            // 1. Devices due today still Pending or In Progress
            $dueToday = JobCard::whereDate('estimated_delivery', $today)
                ->whereIn('status', ['Pending', 'In Progress'])
                ->get(['id', 'order_no', 'device_name', 'customer_name', 'status', 'estimated_delivery']);

            // 2. Jobs where staff requested help
            $needAssistant = JobCard::where('need_assistant', true)
                ->whereNotIn('status', ['Completed', 'Not Completed'])
                ->get(['id', 'order_no', 'device_name', 'customer_name', 'status']);

            // 3. Completed jobs with payment not received
            $unpaidCompleted = JobCard::where('status', 'Completed')
                ->where('payment_received', false)
                ->get(['id', 'order_no', 'device_name', 'customer_name', 'rupees']);

            $totalCount = $dueToday->count() + $needAssistant->count() + $unpaidCompleted->count();

            $view->with('notifData', [
                'total'          => $totalCount,
                'dueToday'       => $dueToday,
                'needAssistant'  => $needAssistant,
                'unpaidCompleted'=> $unpaidCompleted,
            ]);

            // Share store info (logo + name) globally to layout
            $storeInfo = StoreInfo::first();
            $view->with('storeInfo', $storeInfo);

            // Logged-in user name: admin or employee
            $loggedInName = session('admin_name') ?? session('employee_name') ?? 'User';
            $view->with('loggedInName', $loggedInName);
        });
    }
}
