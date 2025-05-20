<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\CollectionAgent;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Fetch roles
        $adminRole = Role::where('role_name', 'admin')->first();
        $staffRole = Role::where('role_name', 'staff')->first();
        $agentRole = Role::where('role_name', 'agent')->first();
        $customerRole = Role::where('role_name', 'customer')->first();

        // Create Admin User
        if ($adminRole) {
            User::create([
                'role_id' => $adminRole->role_id,
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'email' => 'admin@example.com',
                'full_name' => 'Admin User',
                'is_active' => true,
            ]);
        }

        // Create Staff User
        if ($staffRole) {
            User::create([
                'role_id' => $staffRole->role_id,
                'username' => 'staff1',
                'password' => Hash::make('staff123'),
                'email' => 'staff1@example.com',
                'full_name' => 'Staff One',
                'is_active' => true,
            ]);
        }

        // Create Agent User
        if ($agentRole) {
            $agentUser = User::create([
                'role_id' => $agentRole->role_id,
                'username' => 'agent1',
                'password' => Hash::make('agent123'),
                'email' => 'agent1@example.com',
                'full_name' => 'Agent One',
                'is_active' => true,
            ]);

            CollectionAgent::create([
                'user_id' => $agentUser->user_id,
                'agent_code' => 'AGT001',
                'full_name' => 'Agent One',
                'phone' => '1234567890',
                'address' => '123 Agent Street',
                'date_of_joining' => now(),
                'assigned_branch' => 'Main Branch',
                'bank_name' => 'Sample Bank',
                'account_number' => '123456789012',
                'ifsc_code' => 'SBIN0001234',
                'profile_status' => 'active',
            ]);
        }

        // Create Customer User
        if ($customerRole) {
            $customerUser = User::create([
                'role_id' => $customerRole->role_id,
                'username' => 'customer1',
                'password' => Hash::make('customer123'),
                'email' => 'customer1@example.com',
                'full_name' => 'Customer One',
                'is_active' => true,
            ]);

            Customer::create([
                'user_id' => $customerUser->user_id,
                'full_name' => 'Customer One',
                'phone' => '9876543210',
                'address' => '456 Customer Road',
                'profile_status' => 'approved',
                'approved_by' => $adminRole ? User::where('role_id', $adminRole->role_id)->first()->user_id : null,
                'approved_at' => now(),
            ]);
        }
    }
}