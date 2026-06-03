<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'appointments.view',
            'appointments.create',
            'appointments.cancel',
            'appointments.reschedule',
            'services.manage',
            'staff.manage',
            'customers.manage',
            'analytics.view',
            'ai.configure',
            'tenant.settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $super = Role::firstOrCreate(['name' => 'super_admin']);
        $tenantAdmin = Role::firstOrCreate(['name' => 'tenant_admin']);
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

        $super->givePermissionTo(Permission::all());

        $tenantAdmin->givePermissionTo([
            'appointments.view',
            'appointments.cancel',
            'services.manage',
            'staff.manage',
            'customers.manage',
            'analytics.view',
            'ai.configure',
            'tenant.settings',
        ]);

        $staff->givePermissionTo(['appointments.view']);
        $customer->givePermissionTo(['appointments.create', 'appointments.cancel', 'appointments.reschedule']);
    }
}
