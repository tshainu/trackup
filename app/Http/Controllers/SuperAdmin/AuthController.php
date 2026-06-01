<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('super_admin_logged_in')) return redirect()->route('superadmin.dashboard');
        return view('superadmin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = SuperAdmin::where('email', $request->email)
                           ->where('status', 'active')
                           ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        $admin->update(['last_login_at' => now()]);

        session([
            'super_admin_logged_in' => true,
            'super_admin_id'        => $admin->id,
            'super_admin_name'      => $admin->name,
            'super_admin_email'     => $admin->email,
        ]);

        return redirect()->route('superadmin.dashboard');
    }

    public function logout()
    {
        session()->forget(['super_admin_logged_in','super_admin_id','super_admin_name','super_admin_email']);
        return redirect()->route('superadmin.login');
    }
}
