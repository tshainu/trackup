<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function edit()
    {
        $shopId = session('shop_id');
        $store  = $shopId
            ? StoreInfo::firstOrNew(['shop_id' => $shopId])
            : StoreInfo::firstOrNew([]);
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
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $shopId = session('shop_id');
        $store  = $shopId
            ? StoreInfo::firstOrNew(['shop_id' => $shopId])
            : StoreInfo::firstOrNew(['id' => 1]);
        if ($shopId) {
            $validated['shop_id'] = $shopId;
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }

        // Handle logo removal
        if ($request->input('remove_logo') === '1') {
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            $validated['logo'] = null;
        }

        $store->fill($validated)->save();

        return back()->with('success', 'Store settings updated.');
    }
}
