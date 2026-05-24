<?php

namespace App\Services;

use App\Models\SmsSetting;
use App\Models\SmsTemplate;
use App\Models\StoreInfo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SmsService
{
    protected SmsSetting $settings;

    public function __construct()
    {
        $this->settings = SmsSetting::current();
    }

    /**
     * Send a raw SMS message to a phone number.
     */
    public function send(string $phone, string $message): bool
    {
        // Normalize phone
        $phone = preg_replace('/\D/', '', $phone);

        if (!$this->settings->enabled || empty($this->settings->api_key)) {
            // Log to file instead of sending
            $logLine = "[" . now()->toDateTimeString() . "] TO:{$phone} | MSG:{$message}";
            \Illuminate\Support\Facades\Log::channel('daily')->info('[SMS-STUB] ' . $logLine);
            file_put_contents(storage_path('logs/sms.log'), $logLine . PHP_EOL, FILE_APPEND);
            return false;
        }

        try {
            // Generic HTTP SMS API call — works with most REST-based SMS gateways
            // POST params: api_key, sender_id (from), to, message
            $response = Http::timeout(10)->post($this->settings->api_url, [
                'api_key'   => $this->settings->api_key,
                'from'      => $this->settings->sender_id,
                'to'        => $phone,
                'message'   => $message,
            ]);

            if ($response->successful()) {
                Log::info("[SMS] Sent to {$phone}: {$message}");
                return true;
            }

            Log::warning("[SMS] Failed for {$phone}: " . $response->body());
            return false;

        } catch (\Throwable $e) {
            Log::error("[SMS] Exception for {$phone}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Load a template by key, replace placeholders, and send.
     *
     * @param string $key       Template key (e.g. 'job_created')
     * @param string $phone     Recipient phone number
     * @param array  $vars      Placeholder values: ['customer_name' => '...', ...]
     */
    public function sendTemplate(string $key, string $phone, array $vars = []): bool
    {
        $template = SmsTemplate::forKey($key);
        if (!$template) {
            Log::warning("[SMS] No active template for key: {$key}");
            return false;
        }

        // Add store name automatically
        $store = \App\Models\StoreInfo::first();
        $vars['store_name'] = $store?->store_name ?? config('app.name', 'TrackUp');

        $message = $template->message;
        foreach ($vars as $placeholder => $value) {
            $message = str_replace('{' . $placeholder . '}', $value ?? '', $message);
        }

        return $this->send($phone, $message);
    }
}
