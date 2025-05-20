<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed roles
        $roles = [
            ['role_name' => 'admin'],
            ['role_name' => 'staff'],
            ['role_name' => 'agent'],
            ['role_name' => 'customer'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Seed default permissions for staff
        Permission::create([
            'role_id' => Role::where('role_name', 'staff')->first()->role_id,
            'can_add_customer' => true,
            'can_add_agent' => true,
            'can_edit_delete' => true,
            'edit_delete_time_limit' => 24,
        ]);

        // Admin permissions (full access)
        Permission::create([
            'role_id' => Role::where('role_name', 'admin')->first()->role_id,
            'can_add_customer' => true,
            'can_add_agent' => true,
            'can_edit_delete' => true,
            'edit_delete_time_limit' => 0, // No time limit for admin
        ]);
    }
}