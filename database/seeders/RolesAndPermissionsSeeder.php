<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $roles = [
            ['role_name' => 'admin'],
            ['role_name' => 'staff'],
            ['role_name' => 'customer'],
            ['role_name' => 'agent'],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(['role_name' => $roleData['role_name']]);
            
            // Set permissions for each role
            if ($role->role_name === 'admin') {
                Permission::updateOrCreate(
                    ['role_id' => $role->role_id],
                    [
                        'can_add_customer' => true,
                        'can_add_agent' => true,
                        'can_edit_delete' => true,
                        'edit_delete_time_limit' => 0,
                    ]
                );
            } elseif ($role->role_name === 'staff') {
                Permission::updateOrCreate(
                    ['role_id' => $role->role_id],
                    [
                        'can_add_customer' => true,
                        'can_add_agent' => false,
                        'can_edit_delete' => true,
                        'edit_delete_time_limit' => 48,
                    ]
                );
            } elseif ($role->role_name === 'agent') {
                Permission::updateOrCreate(
                    ['role_id' => $role->role_id],
                    [
                        'can_add_customer' => false,
                        'can_add_agent' => false,
                        'can_edit_delete' => true,
                        'edit_delete_time_limit' => 24,
                    ]
                );
            } else {
                // customer role
                Permission::updateOrCreate(
                    ['role_id' => $role->role_id],
                    [
                        'can_add_customer' => false,
                        'can_add_agent' => false,
                        'can_edit_delete' => false,
                        'edit_delete_time_limit' => 0,
                    ]
                );
            }
        }
    }
}