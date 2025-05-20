<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAgent
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->role->role_name === 'agent') {
            return $next($request);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}