<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'gender' => 'required|string|in:Male,Female,Other',
                'device_type' => 'required|string|in:ios,android',
                'device_token' => 'required|string',
            ]);

            return DB::transaction(function () use ($request, $validated) {
                $customerRole = Role::where('role_name', 'customer')->firstOrFail();

                $user = User::create([
                    'role_id' => $customerRole->role_id,
                    'username' => $validated['user_name'],
                    'password' => Hash::make($validated['password']),
                    'email' => $validated['email'],
                    'full_name' => $validated['user_name'],
                    'gender' => $validated['gender'],
                    'is_active' => true,
                ]);

                Log::info('User created successfully', ['user_id' => $user->id]);

                $customerData = [
                    'user_id' => $user->id,
                    'full_name' => $validated['user_name'],
                    'profile_status' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                ];
                $customer = Customer::create($customerData);

                Log::info('Customer created successfully', ['customer_id' => $customer->customer_id]);

                Device::create([
                    'user_id' => $user->id,
                    'device_type' => $validated['device_type'],
                    'device_token' => $validated['device_token'],
                ]);

                return response()->json([
                    'status_code' => 201,
                    'status' => 1,
                    'message' => 'Customer registered successfully',
                    'data' => [
                        'email' => $user->email,
                        'user_id' => $user->id,
                        'user_name' => $user->full_name,
                        'gender' => $user->gender,
                        'role' => $customerRole->role_name,
                        'customer_id' => $customer->customer_id,
                    ],
                ], 201);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'status' => 0,
                'message' => $e->getMessage(),
                'data' => (object) [],
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status_code' => 400,
                'status' => 0,
                'message' => 'Customer role not found',
                'data' => (object) [],
            ], 400);
        } catch (\Exception $e) {
            Log::error('Failed to create customer', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status_code' => 500,
                'status' => 0,
                'message' => 'Failed to register customer',
                'data' => (object) [],
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->firstOrFail();

        try {
            $validated = $request->validate([
                'phone' => 'nullable|string|regex:/^[0-9]{10}$/',
                'address' => 'nullable|string|max:255',
                'photos' => 'nullable|file|mimes:jpg,png|max:2048',
                'aadhar' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
                'passbook' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
                'loan_amount' => 'nullable|numeric|min:1000',
            ]);

            return DB::transaction(function () use ($request, $validated, $customer, $user) {
                // Update customer details
                $customer->update([
                    'phone' => $validated['phone'] ?? $customer->phone,
                    'address' => $validated['address'] ?? $customer->address,
                    'profile_status' => 'pending', // Reset to pending for re-approval
                ]);

                // Handle document uploads
                $documents = ['photo', 'aadhar', 'passbook'];
                foreach ($documents as $docType) {
                    if ($request->hasFile($docType)) {
                        $path = $request->file($docType)->store('documents', 'public');
                        Document::create([
                            'customer_id' => $customer->customer_id,
                            'document_type' => $docType,
                            'file_path' => $path,
                        ]);
                    }
                }

                // Optionally create a loan request (assuming a Loan model exists)
                if (isset($validated['loan_amount'])) {
                    // Example: Loan::create([...]);
                    Log::info('Loan request submitted', ['customer_id' => $customer->customer_id, 'loan_amount' => $validated['loan_amount']]);
                }

                return response()->json([
                    'status_code' => 200,
                    'status' => 1,
                    'message' => 'Profile updated successfully, awaiting approval',
                    'data' => [
                        'email' => $user->email,
                        'user_name' => $user->full_name,
                        'customer_id' => $customer->customer_id,
                        'profile_status' => $customer->profile_status,
                    ],
                ], 200);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'status' => 0,
                'message' => $e->getMessage(),
                'data' => (object) [],
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update profile', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status_code' => 500,
                'status' => 0,
                'message' => 'Failed to update profile',
                'data' => (object) [],
            ], 500);
        }
    }

    public function approveProfile(Request $request, $customer_id)
    {
        $user = $request->user(); // Admin or staff
        $customer = Customer::findOrFail($customer_id);

        try {
            $validated = $request->validate([
                'status' => 'required|string|in:approved,rejected',
            ]);

            return DB::transaction(function () use ($validated, $customer, $user) {
                $customer->update([
                    'profile_status' => $validated['status'],
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);

                Log::info('Customer profile approved/rejected', [
                    'customer_id' => $customer->customer_id,
                    'status' => $validated['status'],
                    'approved_by' => $user->id,
                ]);

                return response()->json([
                    'status_code' => 200,
                    'status' => 1,
                    'message' => 'Customer profile ' . $validated['status'],
                    'data' => [
                        'customer_id' => $customer->customer_id,
                        'profile_status' => $customer->profile_status,
                    ],
                ], 200);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'status' => 0,
                'message' => $e->getMessage(),
                'data' => (object) [],
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to approve profile', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status_code' => 500,
                'status' => 0,
                'message' => 'Failed to approve profile',
                'data' => (object) [],
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->firstOrFail();
        return response()->json([
            'status_code' => 200,
            'status' => 1,
            'message' => 'Customer profile retrieved',
            'data' => [
                'email' => $user->email,
                'user_name' => $user->full_name,
                'gender' => $user->gender,
                'role' => $user->role->role_name,
                'customer_id' => $customer->customer_id,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'profile_status' => $customer->profile_status,
            ],
        ], 200);
    }
}
