<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Shop;

class AdminApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $shop = Shop::where('admin_api_token', $token)
                    ->where('status', 'active')
                    ->first();

        if (!$shop) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->attributes->set('auth_shop', $shop);

        return $next($request);
    }
}
