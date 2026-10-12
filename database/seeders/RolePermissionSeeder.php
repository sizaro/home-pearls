<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // -----------------------------
        // 1️⃣ PERMISSIONS
        // -----------------------------
        $permissions = [

            // Dashboard
            'view dashboard',

            // Users
            'manage users',

            // Products
            'create products',
            'edit products',
            'delete products',

            // Categories
            'create categories',
            'edit categories',
            'delete categories',

            // Variants
            'create product variants',
            'edit product variants',
            'delete product variants',

            // Orders
            'view orders',
            'update orders',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // -----------------------------
        // 2️⃣ ROLES
        // -----------------------------
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $admin      = Role::firstOrCreate(['name' => 'admin']);
        $employee   = Role::firstOrCreate(['name' => 'employee']);
        $customer   = Role::firstOrCreate(['name' => 'customer']);

        // -----------------------------
        // 3️⃣ ASSIGN PERMISSIONS
        // -----------------------------

        // 🔥 Super Admin → everything
        $superAdmin->syncPermissions(Permission::all());

        // 🔥 Admin → full store control (except users)
        $admin->syncPermissions([
            'view dashboard',

            'create products',
            'edit products',
            'delete products',

            'create categories',
            'edit categories',
            'delete categories',

            'create product variants',
            'edit product variants',
            'delete product variants',

            'view orders',
            'update orders',
        ]);

        // 🔥 Employee → limited control (no delete)
        $employee->syncPermissions([
            'view dashboard',

            'create products',
            'edit products',

            'create categories',
            'edit categories',

            'create product variants',
            'edit product variants',

            'view orders',
        ]);

        // 🔥 Customer → basically nothing admin-related
        $customer->syncPermissions([]);

        $this->command->info('Roles and permissions seeded.');
    }
}
