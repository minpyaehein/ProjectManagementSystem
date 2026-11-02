<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $adminRole = Role::query()->updateOrCreate(
                [
                    'name' => 'System Admin',
                ],
                [
                    'description' => 'Full system access',
                    'project_access_scope' => 'all',
                    'customer_access_scope' => 'all',
                    'supplier_access_scope' => 'all',
                ]
            );

            $adminRole->permissions()->sync(
                Permission::query()
                    ->pluck('permission_id')
                    ->map(
                        fn ($permissionId): int =>
                            (int) $permissionId
                    )
                    ->all()
            );

            User::query()->updateOrCreate(
                [
                    'username' => 'superadmin',
                ],
                [
                    'name' => 'Super Administrator',
                    'email' => 'admin@example.com',
                    'phone' => null,
                    'password' => Hash::make('Admin@123'),
                    'role_id' => $adminRole->role_id,
                    'project_id' => null,
                    'status' => 'Active',
                ]
            );
        });
    }
}
