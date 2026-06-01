<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabelSetting;
use Illuminate\Http\Request;

class LabelSettingsController extends Controller
{
    public function edit()
    {
        $settings = LabelSetting::current();
        return view('admin.label-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'width_mm'  => 'required|numeric|min:10|max:300',
            'height_mm' => 'required|numeric|min:10|max:300',
            'font_size' => 'required|integer|min:6|max:24',
        ]);

        $settings = LabelSetting::current();
        $settings->update($data);

        return back()->with('success', 'Label settings saved.');
    }
}
