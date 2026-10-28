<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            /*
             * Projects
             *
             * Projects intentionally do not use a Deactivate permission.
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
                'name' => 'Export Projects',
                'slug' => 'projects.export',
            ],

            /*
             * Customers
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
             * Suppliers
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
             * Users
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
             * Roles and permissions
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
             * Project tasks and financial records
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
             * Activity logs
             */
            [
                'name' => 'View Activity Logs',
                'slug' => 'logs.view',
            ],

            /*
             * Full administration access
             */
            [
                'name' => 'Full Administrator Access',
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
