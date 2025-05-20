<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || $user->role->role_name !== 'admin') {
            return response()->json(['error' => 'Unauthorized: Admin access required'], 403);
        }
        return $next($request);
    }
}