<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappSetting;
use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;

class WhatsappSettingsController extends Controller
{
    public function edit()
    {
        $settings  = WhatsappSetting::current();
        $templates = WhatsappTemplate::orderBy('key')->get()->keyBy('key');
        return view('admin.whatsapp.edit', compact('settings', 'templates'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_url'         => 'nullable|url|max:500',
            'api_key'         => 'nullable|string|max:500',
            'instance_id'     => 'nullable|string|max:200',
            'phone_number_id' => 'nullable|string|max:200',
            'enabled'         => 'nullable|boolean',
            'templates'       => 'nullable|array',
            'templates.*.message' => 'nullable|string|max:1000',
            'templates.*.active'  => 'nullable|boolean',
        ]);

        $settings = WhatsappSetting::current();
        $settings->update([
            'api_url'         => $request->input('api_url'),
            'api_key'         => $request->input('api_key'),
            'instance_id'     => $request->input('instance_id'),
            'phone_number_id' => $request->input('phone_number_id'),
            'enabled'         => $request->has('enabled') ? 1 : 0,
        ]);

        if ($request->has('templates')) {
            foreach ($request->input('templates') as $key => $data) {
                $template = WhatsappTemplate::where('key', $key)->first();
                if ($template) {
                    $template->update([
                        'message' => $data['message'] ?? $template->message,
                        'active'  => isset($data['active']) ? 1 : 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.whatsapp-settings.edit')
            ->with('success', 'WhatsApp settings saved successfully.');
    }

    public function test(Request $request)
    {
        $request->validate([
            'phone'   => 'required|string|max:20',
            'message' => 'required|string|max:500',
        ]);

        $settings = WhatsappSetting::current();

        if (!$settings->enabled || !$settings->api_key) {
            \Log::channel('single')->info('[WhatsApp TEST] To: ' . $request->phone . ' | Msg: ' . $request->message);
            return response()->json([
                'ok'      => false,
                'message' => 'WhatsApp logged to file (not enabled or no API key).',
            ]);
        }

        // Generic HTTP API call — works with UltraMsg, WA Gateway, Green API etc.
        try {
            $response = \Http::timeout(10)->post($settings->api_url, [
                'token'  => $settings->api_key,
                'to'     => $request->phone,
                'body'   => $request->message,
                // Also support phone_number_id for WhatsApp Cloud API
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'type'              => 'text',
                'text'              => ['body' => $request->message],
            ]);

            $ok = $response->successful();
            return response()->json([
                'ok'      => $ok,
                'message' => $ok ? 'WhatsApp message sent!' : 'API responded with error: ' . $response->status(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Request failed: ' . $e->getMessage(),
            ]);
        }
    }
}
