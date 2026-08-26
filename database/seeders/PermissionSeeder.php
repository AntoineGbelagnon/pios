<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'view dashboard',
            'manage products',
            'manage categories',
            'manage brands',
            'manage sales',
            'manage stocks',
            'manage customers',
            'manage expenses',
            'manage cash registers',
            'manage cash',
            'manage purchases',
            'manage returns',
            'manage warranties',
            'manage notifications',
            'manage invoices',
            'manage users',
            'manage settings',
            'view audit log',
        ] as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
