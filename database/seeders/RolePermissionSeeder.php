<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==================== ALL PERMISSIONS ====================
        $permissions = [
            // Patient & Visit
            'view patients', 'create patient', 'edit patient', 'reactivate patient',
            'view visits', 'create visit', 'add vitals',

            // Doctor
            'prescribe medicine', 'order lab tests', 'admit patient', 'discharge patient',

            // Lab
            'enter lab results',

            // Pharmacy
            'issue medicine', 'issue injection',

            // Store
            'issue store items',

            // Billing
            'collect registration', 'generate final bill', 'print receipt',

            // Admin Only
            'manage users', 'manage roles', 'manage lab tests', 'manage medicines',
            'manage wards', 'manage store items', 'manage suppliers', 'manage settings',

            // Reports
            'view daily collection', 'view stock report', 'view expiry report',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ==================== ROLES + PERMISSIONS ====================
        Role::firstOrCreate(['name' => 'Admin'])->givePermissionTo(Permission::all());

        Role::firstOrCreate(['name' => 'Reception'])->syncPermissions([
            'view patients', 'create patient', 'edit patient', 'reactivate patient',
            'view visits', 'create visit', 'add vitals', 'collect registration',
            'generate final bill', 'print receipt',
        ]);

        Role::firstOrCreate(['name' => 'Doctor'])->syncPermissions([
            'view patients', 'view visits', 'prescribe medicine', 'order lab tests',
            'admit patient', 'discharge patient',
        ]);

        Role::firstOrCreate(['name' => 'Lab'])->syncPermissions([
            'enter lab results', 'issue store items',
        ]);

        Role::firstOrCreate(['name' => 'Pharmacy'])->syncPermissions([
            'issue medicine', 'issue injection', 'issue store items',
        ]);

        Role::firstOrCreate(['name' => 'Cashier'])->syncPermissions([
            'generate final bill', 'print receipt',
        ]);

        Role::firstOrCreate(['name' => 'Store'])->syncPermissions([
            'issue store items',
        ]);

        // ==================== ADMIN USER ====================
        $admin = User::updateOrCreate(
            ['email' => 'admin@mana.com'],
            [
                'employee_code' => 'EMP001',
                'name'          => 'Administrator',
                'phone'         => '0624592725',
                'department'    => 'Admin',
                'password'      => bcrypt('admin123'), // Change after first login!
                'is_active'     => true,
            ]
        );

        $admin->assignRole('Admin');

        $this->command->info('Roles & Permissions seeded successfully!');
        $this->command->info('Admin Login → Email: admin@carewell.com | Password: admin123');
    }
}