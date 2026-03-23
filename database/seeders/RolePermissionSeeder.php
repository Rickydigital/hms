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
            'view daily collection', 'view stock report', 'view expiry report', 'manage procedures'
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ==================== ROLES + PERMISSIONS ====================
        $rolesPermissions = [
            'Admin' => Permission::all()->pluck('name')->toArray(),
            'Reception' => [
                'view patients', 'create patient', 'edit patient', 'reactivate patient',
                'view visits', 'create visit', 'add vitals', 'collect registration',
                'generate final bill', 'print receipt',
            ],
            'Doctor' => [
                'view patients', 'view visits', 'prescribe medicine', 'order lab tests',
                'admit patient', 'discharge patient', 'manage procedures',
            ],
            'Lab' => ['enter lab results', 'issue store items'],
            'Pharmacy' => ['issue medicine', 'issue injection', 'issue store items'],
            'Cashier' => ['generate final bill', 'print receipt'],
            'Store' => ['issue store items'],
        ];

        foreach ($rolesPermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->givePermissionTo($perms); // Add permissions without removing existing ones
        }

        // ==================== ADMIN USER ====================
        $admin = User::updateOrCreate(
            ['email' => 'admin@mana.com'],
            [
                'employee_code' => 'EMP001',
                'name'          => 'Administrator',
                'phone'         => '0624592725',
                'department'    => 'Admin',
                'password'      => bcrypt('admin123'),
                'is_active'     => true,
            ]
        );

        $admin->assignRole('Admin');

        $this->command->info('Roles & Permissions seeded successfully!');
        $this->command->info('Admin Login → Email: admin@mana.com | Password: admin123');
    }
}