<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $adminRole = Role::updateOrCreate(
                ['name' => 'System Admin'],
                ['description' => 'Full system access']
            );

            $adminRole->permissions()->sync(
                Permission::query()
                    ->pluck('permission_id')
                    ->all()
            );

            User::updateOrCreate(
                ['name' => 'SuperAdmin'],
                [
                    'email' => 'admin@example.com',
                    'password' => 'Admin@123',
                    'role_id' => $adminRole->role_id,
                    'status' => 'Active',
                ]
            );
        });
    }
}
