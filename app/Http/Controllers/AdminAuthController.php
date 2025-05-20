<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminAuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by username or email
        $user = User::where('username', $request->login)
                    ->orWhere('email', $request->login)
                    ->first();

        // Check if user exists, password matches, and user has admin/staff role
        if ($user && Hash::check($request->password, $user->password)) {
            $role = Role::find($user->role_id);
            if ($role && in_array($role->role_name, ['admin', 'staff'])) {
                Auth::guard('admin')->login($user);
                Session::regenerate();
                return redirect()->route('admin.dashboard');
            }
        }

        // Redirect back with error message
        return back()->withErrors(['login' => 'Invalid credentials or unauthorized role.'])->withInput();
    }

    // Logout function
    public function logout()
    {
        Auth::guard('admin')->logout();
        Session::invalidate();
        Session::regenerateToken();
        return redirect()->route('admin.login');
    }

    // Show reset password form
    public function showResetForm()
    {
        return view('admin.password_reset');
    }
}