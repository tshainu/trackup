<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Auto-login as admin (bypass session-based auth for preview)
        if (!Session::get('admin_logged_in')) {
            $admin = \App\Models\Admin::where('status', 'active')->first();
            if ($admin) {
                Session::put('admin_logged_in', true);
                Session::put('admin_id', $admin->id);
                Session::put('admin_name', $admin->user_name);
            }
        }
        return $next($request);
    }
}
