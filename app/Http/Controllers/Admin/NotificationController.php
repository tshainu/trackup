<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $dueToday = JobCard::whereDate('estimated_delivery', $today)
            ->whereIn('status', ['Pending', 'In Progress'])
            ->orderBy('estimated_delivery')
            ->get();

        $needAssistant = JobCard::where('need_assistant', true)
            ->whereNotIn('status', ['Completed', 'Not Completed'])
            ->orderBy('created_at', 'desc')
            ->get();

        $unpaidCompleted = JobCard::where('status', 'Completed')
            ->where('payment_received', false)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.notifications.index', compact('dueToday', 'needAssistant', 'unpaidCompleted'));
    }

    public function markPaymentReceived(JobCard $jobCard)
    {
        $jobCard->update(['payment_received' => true]);
        return back()->with('success', 'Payment marked as received.');
    }

    public function dismissAssistant(JobCard $jobCard)
    {
        $jobCard->update(['need_assistant' => false]);
        return back()->with('success', 'Assistance request dismissed.');
    }
}
