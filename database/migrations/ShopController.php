<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ShopController extends Controller
{
    // Fruit names pool for password generation
    private static $fruits = [
        'apple','mango','grape','peach','plum','lime','kiwi','pear',
        'fig','date','melon','guava','lemon','papay','berry','olive',
    ];

    private function generateShopId(): string
    {
        // Format: D785 — 1 uppercase letter + 3 digits, unique
        do {
            $id = strtoupper(chr(rand(65, 90))) . rand(100, 999);
        } while (Shop::where('shop_code', $id)->exists());

        return $id;
    }

    private function generatePassword(): string
    {
        // First 4 chars from a fruit name, then 3 random digits
        $fruit = self::$fruits[array_rand(self::$fruits)];
        $prefix = substr($fruit, 0, 4);
        $digits = rand(100, 999);
        return $prefix . $digits;
    }

    public function index(Request $request)
    {
        $query = Shop::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('shop_name', 'like', "%$s%")
                  ->orWhere('shop_code', 'like', "%$s%")
                  ->orWhere('owner_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sort')) {
            $sortDir = $request->sort === 'shop_name' ? 'asc' : 'desc';
            $query->reorder($request->sort, $sortDir);
        }

        $shops = $query->paginate(12)->withQueryString();

        $statusCounts = Shop::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('superadmin.shops.index', compact('shops', 'statusCounts'));
    }

    public function create()
    {
        return view('superadmin.shops.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'shop_name'  => 'required|string|max:100',
            'owner_name' => 'required|string|max:100',
            'email'      => 'required|email|unique:shops,email',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:255',
            'city'       => 'nullable|string|max:100',
            'country'    => 'nullable|string|max:100',
            'notes'      => 'nullable|string',
            'modules'    => 'nullable|array',
            'modules.*'  => 'in:job_orders,field_services',
        ]);

        $shopCode      = $this->generateShopId();
        $adminUsername = 'admin';
        $adminPassword = $this->generatePassword();

        $modules = $request->input('modules', ['job_orders', 'field_services']);

        $shop = Shop::create([
            'shop_name'            => $request->shop_name,
            'shop_code'            => $shopCode,
            'owner_name'           => $request->owner_name,
            'email'                => $request->email,
            'phone'                => $request->phone,
            'address'              => $request->address,
            'city'                 => $request->city,
            'country'              => $request->country ?? 'Sri Lanka',
            'notes'                => $request->notes,
            'modules'              => $modules,
            'admin_username'       => $adminUsername,
            'admin_password_hash'  => Hash::make($adminPassword),
            'admin_plain_password' => $adminPassword,
            'status'               => $request->status ?? 'active',
        ]);

        ShopActivityLog::create([
            'shop_id'      => $shop->id,
            'action'       => 'created',
            'description'  => 'Shop created by super admin',
            'performed_by' => session('super_admin_id'),
        ]);

        return redirect()->route('superadmin.shops.show', $shop)
            ->with('success', "Shop created! Shop ID: <strong>{$shopCode}</strong> &nbsp;|&nbsp; Login: <strong>{$adminUsername}</strong> / <strong>{$adminPassword}</strong>");
    }

    public function show(Shop $shop)
    {
        $logs = $shop->activityLogs()->latest()->take(20)->get();
        return view('superadmin.shops.show', compact('shop', 'logs'));
    }

    public function edit(Shop $shop)
    {
        return view('superadmin.shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $request->validate([
            'shop_name'  => 'required|string|max:100',
            'owner_name' => 'required|string|max:100',
            'email'      => 'required|email|unique:shops,email,' . $shop->id,
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:255',
            'city'       => 'nullable|string|max:100',
            'country'    => 'nullable|string|max:100',
            'notes'      => 'nullable|string',
            'modules'    => 'nullable|array',
            'modules.*'  => 'in:job_orders,field_services',
        ]);

        $modules = $request->input('modules', []);

        $shop->update(array_merge(
            $request->only('shop_name', 'owner_name', 'email', 'phone', 'address', 'city', 'country', 'notes', 'status'),
            ['modules' => $modules]
        ));

        ShopActivityLog::create([
            'shop_id'      => $shop->id,
            'action'       => 'updated',
            'description'  => 'Shop details updated',
            'performed_by' => session('super_admin_id'),
        ]);

        return redirect()->route('superadmin.shops.show', $shop)
            ->with('success', 'Shop updated successfully.');
    }

    public function resetPassword(Shop $shop)
    {
        $newPassword = $this->generatePassword();   // fruit4chars + 3digits

        $shop->update([
            'admin_password_hash'  => Hash::make($newPassword),
            'admin_plain_password' => $newPassword,
        ]);

        ShopActivityLog::create([
            'shop_id'      => $shop->id,
            'action'       => 'password_reset',
            'description'  => 'Admin password reset by super admin',
            'performed_by' => session('super_admin_id'),
        ]);

        return redirect()->route('superadmin.shops.show', $shop)
            ->with('success', "Password reset! New password: <strong>{$newPassword}</strong>");
    }

    public function updateStatus(Request $request, Shop $shop)
    {
        $request->validate(['status' => 'required|in:active,suspended,pending']);
        $old = $shop->status;
        $shop->update(['status' => $request->status]);

        ShopActivityLog::create([
            'shop_id'      => $shop->id,
            'action'       => 'status_changed',
            'description'  => "Status changed from {$old} to {$request->status}",
            'performed_by' => session('super_admin_id'),
        ]);

        return back()->with('success', 'Status updated.');
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();
        return redirect()->route('superadmin.shops.index')
            ->with('success', 'Shop deleted.');
    }
}
