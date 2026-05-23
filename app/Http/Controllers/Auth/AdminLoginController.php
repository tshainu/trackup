<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
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
            'user_name' => 'required|string',
            'password'  => 'required|string',
        ]);

        $admin = Admin::where('user_name', $request->user_name)
                      ->where('status', 'active')
                      ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['user_name' => 'Invalid credentials.'])->withInput();
        }

        Session::put('admin_logged_in', true);
        Session::put('admin_id', $admin->id);
        Session::put('admin_name', $admin->user_name);

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        Session::forget(['admin_logged_in', 'admin_id', 'admin_name']);
        return redirect()->route('admin.login');
    }
}
