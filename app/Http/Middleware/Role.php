<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Role
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (Auth::user() && in_array(Auth::user()->role->role_name, $roles)) {
            return $next($request);
        }

        return response()->json([
            'status_code' => 403,
            'status'=> 0,
            'message' => 'Unauthorized: Access restricted to ' . implode(', ', $roles),
            'data' => [],
        ], 403);
    }
}