<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmployeeAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Auto-login as first employee (bypass session-based auth for preview)
        if (!Session::get('employee_logged_in')) {
            $emp = \App\Models\Employee::where('status', 'active')->first();
            if ($emp) {
                Session::put('employee_logged_in', true);
                Session::put('employee_id', $emp->id);
                Session::put('employee_name', $emp->employee_name);
            }
        }
        return $next($request);
    }
}
