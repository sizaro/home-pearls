<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // -----------------------------
        // 1️⃣ Create Permissions
        // -----------------------------
        $permissions = [
            'view dashboard',
            'manage users',
            'view reports',
            'create reports',
            'manage system',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // -----------------------------
        // 2️⃣ Create Roles
        // -----------------------------
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super admin']);

        // -----------------------------
        // 3️⃣ Assign Permissions to Roles
        // -----------------------------
        $superAdminRole->givePermissionTo(Permission::all()); // all permissions
        $employeeRole->givePermissionTo(['view dashboard', 'view reports']);
        $customerRole->givePermissionTo(['view dashboard']);

        // -----------------------------
        // 4️⃣ Create Users
        // -----------------------------
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@homepearls.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin123!'),
            ]
        );

        $superAdmin->assignRole($superAdminRole);

        $employee = User::firstOrCreate(
            ['email' => 'employee@homepearls.com'],
            [
                'name' => 'Employee User',
                'password' => Hash::make('Employee123!'),
            ]
        );

        $employee->assignRole($employeeRole);

        $customer = User::firstOrCreate(
            ['email' => 'customer@homepearls.com'],
            [
                'name' => 'Customer User',
                'password' => Hash::make('Customer123!'),
            ]
        );

        $customer->assignRole($customerRole);

        $this->command->info('✅ Roles, permissions, and users seeded successfully.');
    }
}