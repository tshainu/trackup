<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Employee;

class TechnicianApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $employee = Employee::where('api_token', $token)->whereIn('status', ['Active','active'])->first();

        if (!$employee) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->merge(['auth_employee' => $employee]);
        $request->attributes->set('auth_employee', $employee);

        return $next($request);
    }
}
