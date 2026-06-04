<?php

namespace App\Services;

use App\Models\WhatsappSetting;
use App\Models\WhatsappTemplate;
use App\Models\StoreInfo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected WhatsappSetting $settings;

    public function __construct()
    {
        $this->settings = WhatsappSetting::current();
    }

    public function sendTemplate(string $key, string $phone, array $vars = []): bool
    {
        $template = WhatsappTemplate::where('key', $key)->where('active', true)->first();
        if (!$template) {
            Log::warning("[WA] Template '{$key}' not found or inactive.");
            return false;
        }

        $store   = StoreInfo::current();
        $message = $template->message;
        $vars['store_name'] = $store?->store_name ?? config('app.name');

        foreach ($vars as $placeholder => $value) {
            $message = str_replace('{' . $placeholder . '}', $value, $message);
        }

        return $this->send($phone, $message);
    }

    public function send(string $phone, string $message): bool
    {
        if (!$this->settings->enabled || !$this->settings->api_key) {
            Log::channel('single')->info("[WA Logged] To: {$phone} | {$message}");
            return true; // logged but not sent
        }

        try {
            $response = Http::timeout(10)->post($this->settings->api_url, [
                'token'          => $this->settings->api_key,
                'to'             => $phone,
                'body'           => $message,
                'instance_id'    => $this->settings->instance_id,
                'phone_number_id'=> $this->settings->phone_number_id,
            ]);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('[WA] Send failed: ' . $e->getMessage());
            return false;
        }
    }
}
