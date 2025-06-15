<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view projects',
            'create projects',
            'edit projects',
            'delete projects',
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'view activities',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'project-manager']);
        Role::create(['name' => 'member']);

        $role = Role::findByName('member');
        $role->givePermissionTo([
            'view projects',
            'view tasks',
        ]);

        $role = Role::findByName('project-manager');
        $role->givePermissionTo([
            'view projects',
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks'
        ]);

        $role = Role::findByName('admin');
        $role->givePermissionTo(Permission::all());

        // Assign 'view activities' permission to admin role
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo('view activities');
    }
}
