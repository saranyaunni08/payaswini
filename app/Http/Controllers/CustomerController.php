<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\Role;
use App\Models\Document;
use App\Models\ProfileStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('user')->get();
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        $user = Auth::guard('admin')->user();
        $permissions = $user->role->permissions;

        if ($user->role->role_name !== 'admin' && !$permissions->can_add_customer) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to add customers.');
        }

        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        try {
            Log::info('Starting customer creation process', ['user_id' => Auth::guard('admin')->user()->user_id]);

            $user = Auth::guard('admin')->user();
            $permissions = $user->role->permissions;

            if ($user->role->role_name !== 'admin' && !$permissions->can_add_customer) {
                Log::warning('Permission denied for adding customer', ['user_id' => $user->user_id]);
                return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to add customers.');
            }

            Log::info('Validating request data', $request->all());
            $validated = $request->validate([
                'full_name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'photo' => 'nullable|file|mimes:jpg,png|max:2048',
                'aadhar' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
                'passbook' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            ]);

            Log::info('Creating user with customer role');
            $customerRole = Role::where('role_name', 'customer')->first();
            if (!$customerRole) {
                Log::error('Customer role not found');
                return redirect()->route('admin.customers.index')->with('error', 'Customer role not found. Please ensure roles are properly seeded.');
            }

            $user = User::create([
                'role_id' => $customerRole->role_id,
                'username' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email' => $validated['email'],
                'full_name' => $validated['full_name'],
            ]);

            Log::info('User created', ['user_id' => $user->user_id]);

            Log::info('Creating customer');
            $customer = Customer::create([
                'user_id' => $user->user_id,
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'profile_status' => 'pending',
            ]);

            Log::info('Customer created', ['customer_id' => $customer->customer_id]);

            Log::info('Handling document uploads');
            $documents = ['photo', 'aadhar', 'passbook'];
            foreach ($documents as $docType) {
                if ($request->hasFile($docType)) {
                    $path = $request->file($docType)->store('documents', 'public');
                    Document::create([
                        'customer_id' => $customer->customer_id,
                        'document_type' => $docType,
                        'file_path' => $path,
                    ]);
                    Log::info('Document uploaded', ['type' => $docType, 'path' => $path]);
                }
            }

            ProfileStatusLog::create([
                'customer_id' => $customer->customer_id,
                'changed_by' => Auth::guard('admin')->user()->user_id,
                'old_status' => null,
                'new_status' => 'pending',
            ]);

            Log::info('Customer creation completed successfully');
            return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create customer', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.customers.index')->with('error', 'Failed to create customer. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $customer = Customer::with('user', 'documents', 'loans', 'statusLogs.changedBy')->findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::with('user', 'documents')->findOrFail($id);
        $user = Auth::guard('admin')->user();
        $permissions = $user->role->permissions;

        if ($user->role->role_name !== 'admin' && (!$permissions->can_edit_delete || now()->diffInHours($customer->created_at) > $permissions->edit_delete_time_limit)) {
            $timeLimit = $user->role->role_name === 'staff' ? '48 hours' : '24 hours';
            return redirect()->route('admin.customers.index')->with('error', "You do not have permission to edit this customer. {$user->role->role_name} can only edit within {$timeLimit} of creation.");
        }

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $user = Auth::guard('admin')->user();
        $permissions = $user->role->permissions;

        if ($user->role->role_name !== 'admin' && (!$permissions->can_edit_delete || now()->diffInHours($customer->created_at) > $permissions->edit_delete_time_limit)) {
            $timeLimit = $user->role->role_name === 'staff' ? '48 hours' : '24 hours';
            return redirect()->route('admin.customers.index')->with('error', "You do not have permission to update this customer. {$user->role->role_name} can only update within {$timeLimit} of creation.");
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $customer->user->user_id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|file|mimes:jpg,png|max:2048',
            'aadhar' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'passbook' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        Log::info('Updating customer details', ['customer_id' => $customer->customer_id]);
        $customer->user->update([
            'email' => $validated['email'],
            'full_name' => $validated['full_name'],
        ]);

        $customer->update([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        Log::info('Handling document updates for customer', ['customer_id' => $customer->customer_id]);
        $documents = ['photo', 'aadhar', 'passbook'];
        foreach ($documents as $docType) {
            if ($request->hasFile($docType)) {
                Log::info("New $docType uploaded, processing update", ['customer_id' => $customer->customer_id]);

                // Find the existing document
                $oldDoc = $customer->documents()->where('document_type', $docType)->first();
                if ($oldDoc) {
                    Log::info("Deleting old $docType", ['path' => $oldDoc->file_path]);
                    // Delete the old file from storage
                    if (Storage::disk('public')->exists($oldDoc->file_path)) {
                        Storage::disk('public')->delete($oldDoc->file_path);
                        Log::info("Old $docType deleted successfully", ['path' => $oldDoc->file_path]);
                    } else {
                        Log::warning("Old $docType file not found in storage", ['path' => $oldDoc->file_path]);
                    }
                    // Delete the old document record
                    $oldDoc->delete();
                }

                // Upload the new file
                $path = $request->file($docType)->store('documents', 'public');
                Log::info("New $docType uploaded", ['path' => $path]);

                // Create a new document record
                Document::create([
                    'customer_id' => $customer->customer_id,
                    'document_type' => $docType,
                    'file_path' => $path,
                ]);
                Log::info("New $docType record created", ['path' => $path]);
            }
        }

        Log::info('Customer update completed successfully', ['customer_id' => $customer->customer_id]);
        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $user = Auth::guard('admin')->user();
        $permissions = $user->role->permissions;

        if ($user->role->role_name !== 'admin' && (!$permissions->can_edit_delete || now()->diffInHours($customer->created_at) > $permissions->edit_delete_time_limit)) {
            $timeLimit = $user->role->role_name === 'staff' ? '48 hours' : '24 hours';
            return redirect()->route('admin.customers.index')->with('error', "You do not have permission to delete this customer. {$user->role->role_name} can only delete within {$timeLimit} of creation.");
        }

        foreach ($customer->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
        }

        $customer->user->delete();
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function updateProfileStatus(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $user = Auth::guard('admin')->user();

        if ($user->role->role_name !== 'admin' && $user->role->role_name !== 'staff') {
            return redirect()->route('admin.customers.index')->with('error', 'Only admins and office staff can update profile status.');
        }

        $validated = $request->validate([
            'profile_status' => 'required|in:pending,approved,rejected',
        ]);

        ProfileStatusLog::create([
            'customer_id' => $customer->customer_id,
            'changed_by' => $user->user_id,
            'old_status' => $customer->profile_status,
            'new_status' => $validated['profile_status'],
        ]);

        $updateData = [
            'profile_status' => $validated['profile_status'],
        ];

        if (in_array($validated['profile_status'], ['approved', 'rejected'])) {
            $updateData['approved_by'] = $user->user_id;
            $updateData['approved_at'] = now();
        } else {
            $updateData['approved_by'] = null;
            $updateData['approved_at'] = null;
        }

        $customer->update($updateData);

        return redirect()->route('admin.customers.index')->with('success', 'Profile status updated successfully.');
    }
}