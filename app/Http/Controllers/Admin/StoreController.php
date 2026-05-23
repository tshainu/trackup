<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreInfo;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function edit()
    {
        $store = StoreInfo::firstOrNew([]);
        return view('admin.store.edit', compact('store'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name'      => 'required|string|max:255',
            'registration_no' => 'nullable|string|max:100',
            'store_address'   => 'nullable|string|max:500',
            'phone_no1'       => 'nullable|string|max:20',
            'phone_no2'       => 'nullable|string|max:20',
            'owner_name'      => 'nullable|string|max:255',
            'owner_phoneno'   => 'nullable|string|max:20',
            'owner_address'   => 'nullable|string|max:500',
        ]);

        StoreInfo::updateOrCreate(['id' => 1], $validated);

        return back()->with('success', 'Store settings updated.');
    }
}
