<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\CollectionAgent;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'gender' => 'required|string|in:Male,Female,Other',
            'device_type' => 'required|string|in:ios,android',
            'device_token' => 'required|string',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                $agentRole = Role::where('role_name', 'agent')->firstOrFail();

                $user = User::create([
                    'role_id' => $agentRole->role_id,
                    'username' => $validated['user_name'],
                    'password' => Hash::make($validated['password']),
                    'email' => $validated['email'],
                    'full_name' => $validated['user_name'],
                    'gender' => $validated['gender'],
                    'is_active' => true,
                ]);

                Log::info('User created successfully', ['user_id' => $user->id]);

                $agentData = [
                    'user_id' => $user->id,
                    'agent_code' => 'AGT' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                    'full_name' => $validated['user_name'],
                    'profile_status' => 'active',
                    'date_of_joining' => now(),
                ];
                $agent = CollectionAgent::create($agentData);

                Log::info('Agent created successfully', ['agent_id' => $agent->agent_id]);

                Device::create([
                    'user_id' => $user->id,
                    'device_type' => $validated['device_type'],
                    'device_token' => $validated['device_token'],
                ]);

                return response()->json([
                    'status_code' => 201,
                    'status' => 1,
                    'message' => 'Agent registered successfully',
                    'data' => [
                        'email' => $user->email,
                        'user_id' => $user->id,
                        'user_name' => $user->full_name,
                        'gender' => $user->gender,
                        'role' => $agentRole->role_name,
                        'agent_id' => $agent->agent_id,
                    ],
                ], 201);
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status_code' => 400,
                'status' => 0,
                'message' => 'Agent role not found',
            'data' => (object) [],
            ], 400);
        } catch (\Exception $e) {
            Log::error('Failed to create agent', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status_code' => 500,
                'status' => 0,
                'message' => 'Failed to register agent',
            'data' => (object) [],
            ], 500);
        }
    }
}