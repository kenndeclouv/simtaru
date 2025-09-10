<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

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
        $commonFeatures = [
            'permohonan',
            'template',
            'roles',
            'key-storage',
            'users',
            'logs',
        ];
        $actions = ['view', 'create', 'edit', 'delete'];
        $specialFeatures = [
            'permohonan' => ['approve'],
            'logs' => ['download'],
            'performance' => ['view'],
            'route-list' => ['view'],
            'audit-trail' => ['view'],
        ];
        // create permissions
        $permissions = [];
        foreach ($commonFeatures as $feature) {
            foreach ($actions as $action) {
                $permissions[] = Permission::createOrFirst(['name' => $action . ' ' . $feature]);
            }
        }
        foreach ($specialFeatures as $feature => $actions) {
            foreach ($actions as $action) {
                $permissions[] = Permission::createOrFirst(['name' => $action . ' ' . $feature]);
            }
        }
    }
}
