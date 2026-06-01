<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('super_admin_logged_in')) {
            return redirect()->route('superadmin.login');
        }
        return $next($request);
    }
}
