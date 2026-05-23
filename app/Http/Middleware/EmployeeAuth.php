<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmployeeAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('employee_logged_in')) {
            return redirect()->route('employee.login')->with('error', 'Please log in.');
        }
        return $next($request);
    }
}
