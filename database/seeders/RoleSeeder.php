<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin',
            'admin',
            'panel_user',
            'user',
        ];

        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName);
        }

        $superAdmin = Role::where('name', 'super_admin')->first();

        if ($superAdmin) {
            // Grant all existing permissions to super_admin
            $superAdmin->givePermissionTo(Permission::pluck('name')->all());
            // Ensure impersonation permission exists and grant it
            $impersonate = Permission::firstOrCreate(['name' => 'users.impersonate', 'guard_name' => 'web']);
            $superAdmin->givePermissionTo($impersonate);
        }

        $user = User::where('email', 'test@example.com')->first();

        if ($user && $superAdmin) {
            $user->assignRole($superAdmin);
        }
    }
}
