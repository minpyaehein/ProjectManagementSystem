<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            /*
            |--------------------------------------------------------------------------
            | Projects
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Projects',
                'slug' => 'projects.view',
            ],
            [
                'name' => 'Create Projects',
                'slug' => 'projects.create',
            ],
            [
                'name' => 'Edit Projects',
                'slug' => 'projects.edit',
            ],
            [
                'name' => 'Deactivate or Reactivate Projects',
                'slug' => 'projects.deactivate',
            ],
            [
                'name' => 'Export Projects',
                'slug' => 'projects.export',
            ],

            /*
            |--------------------------------------------------------------------------
            | Project Documents
            |--------------------------------------------------------------------------
            |
            | Downloads use projects.view and uploads/removal use projects.edit,
            | so separate document permissions are not required.
            */

            /*
            |--------------------------------------------------------------------------
            | Customers
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Customers',
                'slug' => 'customers.view',
            ],
            [
                'name' => 'Create Customers',
                'slug' => 'customers.create',
            ],
            [
                'name' => 'Edit Customers',
                'slug' => 'customers.edit',
            ],
            [
                'name' => 'Deactivate or Reactivate Customers',
                'slug' => 'customers.deactivate',
            ],

            /*
            |--------------------------------------------------------------------------
            | Suppliers
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Suppliers',
                'slug' => 'suppliers.view',
            ],
            [
                'name' => 'Create Suppliers',
                'slug' => 'suppliers.create',
            ],
            [
                'name' => 'Edit Suppliers',
                'slug' => 'suppliers.edit',
            ],
            [
                'name' => 'Deactivate or Reactivate Suppliers',
                'slug' => 'suppliers.deactivate',
            ],

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Users',
                'slug' => 'users.view',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'users.create',
            ],
            [
                'name' => 'Edit Users',
                'slug' => 'users.edit',
            ],
            [
                'name' => 'Deactivate or Reactivate Users',
                'slug' => 'users.deactivate',
            ],
            [
                'name' => 'Reset User Passwords',
                'slug' => 'users.reset-password',
            ],

            /*
            |--------------------------------------------------------------------------
            | Roles and Permissions
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Roles',
                'slug' => 'roles.view',
            ],
            [
                'name' => 'Create Roles',
                'slug' => 'roles.create',
            ],
            [
                'name' => 'Edit Roles',
                'slug' => 'roles.edit',
            ],
            [
                'name' => 'Deactivate or Reactivate Roles',
                'slug' => 'roles.deactivate',
            ],

            /*
            |--------------------------------------------------------------------------
            | Project Tasks and Financial Records
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Manage Project Tasks',
                'slug' => 'project-tasks.manage',
            ],
            [
                'name' => 'Manage Project Financials',
                'slug' => 'project-financials.manage',
            ],

            /*
            |--------------------------------------------------------------------------
            | Activity Logs
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Activity Logs',
                'slug' => 'logs.view',
            ],

            /*
            |--------------------------------------------------------------------------
            | Full Administration Access
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Full Administrator Access',
                'slug' => 'admin.full',
            ],
        ];

        DB::transaction(function () use ($permissions): void {
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
        });
    }
}
