<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'Read Projects',
                'slug' => 'projects.view',
            ],
            [
                'name' => 'Create Projects',
                'slug' => 'projects.create',
            ],
            [
                'name' => 'Create / Edit Projects',
                'slug' => 'projects.edit',
            ],
            [
                'name' => 'Deactivate / Reactivate Projects',
                'slug' => 'projects.deactivate',
            ],
            [
                'name' => 'Read Customers',
                'slug' => 'customers.view',
            ],
            [
                'name' => 'Create / Edit Customers',
                'slug' => 'customers.edit',
            ],
            [
                'name' => 'Deactivate / Reactivate Customers',
                'slug' => 'customers.deactivate',
            ],
            [
                'name' => 'Read Suppliers',
                'slug' => 'suppliers.view',
            ],
            [
                'name' => 'Create / Edit Suppliers',
                'slug' => 'suppliers.edit',
            ],
            [
                'name' => 'Deactivate / Reactivate Suppliers',
                'slug' => 'suppliers.deactivate',
            ],
            [
                'name' => 'Manage Project Tasks',
                'slug' => 'project-tasks.manage',
            ],
            [
                'name' => 'Manage Revenue',
                'slug' => 'project-financials.manage',
            ],
            [
                'name' => 'Print / Export Project List',
                'slug' => 'projects.export',
            ],
            [
                'name' => 'Manage Users',
                'slug' => 'users.manage',
            ],
            [
                'name' => 'Manage Roles',
                'slug' => 'roles.manage',
            ],
            [
                'name' => 'Full Admin',
                'slug' => 'admin.full',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                [
                    'slug' => $permission['slug'],
                ],
                [
                    'name' => $permission['name'],
                ]
            );
        }
    }
}
