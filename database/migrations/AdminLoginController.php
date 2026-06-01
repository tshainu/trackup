<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminLoginController extends Controller
{
    public function showLogin()
    {
        if (Session::get('admin_logged_in')) return redirect()->route('admin.dashboard');
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'shop_id'   => 'required|string',
            'user_name' => 'required|string',
            'password'  => 'required|string',
        ]);

        // Verify the shop exists and is active
        $shop = Shop::where('shop_code', strtoupper(trim($request->shop_id)))
                    ->where('status', 'active')
                    ->first();

        if (!$shop) {
            return back()
                ->withErrors(['shop_id' => 'Invalid or inactive Shop ID.'])
                ->withInput();
        }

        // Verify username matches this shop's admin_username
        if (strtolower(trim($request->user_name)) !== strtolower($shop->admin_username)) {
            return back()
                ->withErrors(['user_name' => 'Invalid credentials.'])
                ->withInput();
        }

        // Verify password against this shop's hashed password
        if (!Hash::check($request->password, $shop->admin_password_hash)) {
            return back()
                ->withErrors(['user_name' => 'Invalid credentials.'])
                ->withInput();
        }

        // Update last active timestamp for the shop
        $shop->update(['last_active_at' => now()]);

        // Also look up the Admin record (keep backward compat if needed)
        $admin = Admin::where('status', 'active')->first();

        Session::put('admin_logged_in', true);
        Session::put('admin_id',        $admin ? $admin->id : $shop->id);
        Session::put('admin_name',      $shop->admin_username);
        Session::put('shop_id',         $shop->id);
        Session::put('shop_code',       $shop->shop_code);
        Session::put('shop_name',       $shop->shop_name);
        Session::put('shop_modules',    $shop->modules ?? ['job_orders', 'field_services']);

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        Session::forget(['admin_logged_in', 'admin_id', 'admin_name', 'shop_id', 'shop_code', 'shop_name', 'shop_modules']);
        return redirect()->route('admin.login');
    }
}
