<?php

// app/Http/Controllers/ApiController.php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function createCustomer(Request $request)
    {
        $user = $request->user();
        $role = Role::where('role_name', $user->role->role_name)->first();
        $permissions = $role->permissions;

        if (!$permissions->can_add_customer && $user->role->role_name !== 'admin') {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'role_id' => Role::where('role_name', 'customer')->first()->role_id,
            'username' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email' => $validated['email'],
            'full_name' => $validated['full_name'],
        ]);

        Customer::create([
            'user_id' => $user->user_id,
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        return response()->json(['message' => 'Customer created successfully']);
    }

    public function recordPayment(Request $request)
    {
        $user = $request->user();
        $role = Role::where('role_name', $user->role->role_name)->first();

        if ($user->role->role_name !== 'admin' && $user->role->role_name !== 'agent') {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,loan_id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,cheque,online',
        ]);

        Payment::create([
            'loan_id' => $validated['loan_id'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'recorded_by' => $user->user_id,
        ]);

        return response()->json(['message' => 'Payment recorded successfully']);
    }

    public function updatePermissions(Request $request)
    {
        $user = $request->user();
        if ($user->role->role  !== 'admin') {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,role_id',
            'can_add_customer' => 'nullable|boolean',
            'can_add_agent' => 'nullable|boolean',
            'can_edit_delete' => 'nullable|boolean',
            'edit_delete_time_limit' => 'nullable|integer|min:0',
        ]);

        $permission = Permission::where('role_id', $validated['role_id'])->firstOrFail();
        $permission->update($validated);

        return response()->json(['message' => 'Permissions updated successfully']);
    }

    public function getCustomerLoans(Request $request, $customerId)
    {
        $user = $request->user();
        $customer = Customer::findOrFail($customerId);

        if ($user->role->role_name !== 'admin' && $user->user_id !== $customer->user_id) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $loans = $customer->loans()->with(['payments', 'agent'])->get();
        return response()->json(['loans' => $loans]);
    }
}