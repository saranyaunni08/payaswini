<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_type' => 'nullable|string|in:ios,android',
            'device_token' => 'nullable|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status_code' => 401,
                'status' => 0,
                'message' => 'Invalid credentials',
            'data' => (object) [],
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status_code' => 200,
            'status' => 1,
            'message' => 'Login success',
            'data' => [
                'email' => $user->email,
                'user_id' => $user->id,
                'full_name' => $user->full_name,
                'role' => $user->role->role_name,
                'access_token' => $token,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status_code' => 401,
                'status' => 0,
                'message' => 'Unauthenticated',
            'data' => (object) [],
            ], 401);
        }
        $user->currentAccessToken()->delete();
        return response()->json([
            'status_code' => 200,
            'status' => 1,
            'message' => 'Logged out',
            'data' => (object) [],
        ], 200);
    }
}