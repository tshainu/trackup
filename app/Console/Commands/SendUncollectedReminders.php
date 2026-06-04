<?php

namespace App\Console\Commands;

use App\Models\JobCard;
use App\Models\WhatsappSetting;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendUncollectedReminders extends Command
{
    protected $signature   = 'trackup:send-uncollected-reminders';
    protected $description = 'Send WhatsApp reminders for uncollected completed jobcards';

    public function handle(WhatsappService $wa): int
    {
        $settings = WhatsappSetting::current();

        if (! $settings->uncollected_reminder_enabled) {
            $this->info('Uncollected reminders disabled — skipping.');
            return self::SUCCESS;
        }

        $maxCount       = (int) ($settings->uncollected_reminder_count ?? 3);
        $intervalHours  = (int) ($settings->uncollected_reminder_interval_hours ?? 24);

        $cutoff = Carbon::now()->subHours($intervalHours);

        // Uncollected = Completed + payment_received = 0
        $jobs = JobCard::where('status', 'Completed')
            ->where('payment_received', 0)
            ->where('reminder_sent_count', '<', $maxCount)
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_reminder_sent_at')
                  ->orWhere('last_reminder_sent_at', '<=', $cutoff);
            })
            ->get();

        if ($jobs->isEmpty()) {
            $this->info('No uncollected jobs eligible for reminders.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($jobs as $job) {
            if (empty($job->phone_no)) {
                continue;
            }

            $success = $wa->sendTemplate('uncollected_reminder', $job->phone_no, [
                'customer_name' => $job->customer_name,
                'job_no'        => $job->job_no ?? $job->id,
                'device'        => trim(($job->device_name ?? '') . ' ' . ($job->device_brand ?? '')),
                'days'          => (int) Carbon::parse($job->updated_at)->diffInDays(now()),
            ]);

            if ($success) {
                $job->increment('reminder_sent_count');
                $job->update(['last_reminder_sent_at' => now()]);
                $sent++;
                $this->line("  ✓ Sent to {$job->customer_name} ({$job->phone_no}) — job #{$job->id}");
            } else {
                $this->warn("  ✗ Failed for job #{$job->id}");
                Log::warning("[UncollectedReminder] Failed for JobCard #{$job->id}");
            }
        }

        $this->info("Done. Sent {$sent} / {$jobs->count()} reminders.");
        return self::SUCCESS;
    }
}
