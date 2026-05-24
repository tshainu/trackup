<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsSetting;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;

class SmsSettingsController extends Controller
{
    public function edit()
    {
        $settings  = SmsSetting::current();
        $templates = SmsTemplate::orderBy('key')->get()->keyBy('key');
        return view('admin.sms.edit', compact('settings', 'templates'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_url'   => 'nullable|url|max:500',
            'api_key'   => 'nullable|string|max:500',
            'sender_id' => 'nullable|string|max:100',
            'enabled'   => 'nullable|boolean',
            // Template messages
            'templates'                             => 'nullable|array',
            'templates.*.message'                   => 'nullable|string|max:500',
            'templates.*.active'                    => 'nullable|boolean',
        ]);

        // Update settings
        $settings = SmsSetting::current();
        $settings->update([
            'api_url'   => $request->input('api_url'),
            'api_key'   => $request->input('api_key'),
            'sender_id' => $request->input('sender_id'),
            'enabled'   => $request->has('enabled') ? 1 : 0,
        ]);

        // Update each template
        if ($request->has('templates')) {
            foreach ($request->input('templates') as $key => $data) {
                $template = SmsTemplate::where('key', $key)->first();
                if ($template) {
                    $template->update([
                        'message' => $data['message'] ?? $template->message,
                        'active'  => isset($data['active']) ? 1 : 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.sms-settings.edit')
            ->with('success', 'SMS settings saved successfully.');
    }

    public function test(Request $request)
    {
        $request->validate([
            'phone'   => 'required|string|max:20',
            'message' => 'required|string|max:300',
        ]);

        $sms = new \App\Services\SmsService();
        $ok  = $sms->send($request->phone, $request->message);

        return response()->json([
            'ok'      => $ok,
            'message' => $ok ? 'SMS sent successfully.' : 'SMS logged to file (no API key configured or disabled).',
        ]);
    }
}
