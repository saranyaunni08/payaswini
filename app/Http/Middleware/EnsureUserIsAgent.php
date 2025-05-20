<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAgent
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        // Temporarily remove role check
        return response()->json(['debug' => 'Middleware passed, user authenticated']);
        // return $next($request);
    }
}