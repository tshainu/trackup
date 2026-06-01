<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;

class DashboardController extends Controller
{
    public function index()
    {
        $totalShops    = Shop::count();
        $activeShops   = Shop::where('status', 'active')->count();
        $suspendedShops= Shop::where('status', 'suspended')->count();
        $pendingShops  = Shop::where('status', 'pending')->count();
        $onlineShops   = Shop::where('status', 'active')
                             ->where('last_active_at', '>=', now()->subMinutes(15))
                             ->count();

        $thisMonth = Shop::whereMonth('created_at', now()->month)
                         ->whereYear('created_at', now()->year)
                         ->count();
        $thisYear  = Shop::whereYear('created_at', now()->year)->count();

        // Monthly chart data (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'count' => Shop::whereYear('created_at', $date->year)
                               ->whereMonth('created_at', $date->month)
                               ->count(),
            ];
        }

        // Recent shops
        $recentShops = Shop::latest()->take(5)->get();

        // This month grid
        $thisMonthShops = Shop::whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year)
                              ->latest()->get();

        return view('superadmin.dashboard', compact(
            'totalShops','activeShops','suspendedShops','pendingShops',
            'onlineShops','thisMonth','thisYear','monthlyData',
            'recentShops','thisMonthShops'
        ));
    }
}
