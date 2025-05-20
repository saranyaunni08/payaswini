<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsCustomer
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() && Auth::user()->role->role_name === 'customer') {
            return $next($request);
        }

        return response()->json([
            'status_code' => 403,
            'status' => 0,
            'message' => 'Unauthorized: Customer access required',
            'data' => [],
        ], 403);
    }
}