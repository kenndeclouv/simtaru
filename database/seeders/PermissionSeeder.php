<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ["Super Admin", "User"];
        foreach ($roles as $role) {
            Role::createOrFirst(['name' => $role]);
        }

        $features = [
            'permohonan',
            'template',
            'roles',
            'key-storage',
            'users',
        ];
        $actions = ['view', 'create', 'edit', 'delete'];

        $permissions = [];
        foreach ($features as $feature) {
            foreach ($actions as $action) {
                $permissions[] = Permission::createOrFirst(['name' => $action . ' ' . $feature]);
            }
        }

        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $permission->assignRole('Super Admin');
        }
    }
}
