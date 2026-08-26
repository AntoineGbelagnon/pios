<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'propriétaire', 'manager', 'caissier', 'vendeur', 'magasinier', 'comptable', 'livreur', 'responsable achats', 'responsable stocks'];
        $permissions = Permission::query()->pluck('name')->all();
        foreach (Company::query()->get() as $company) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);
            foreach ($roles as $role) {
                $roleModel = Role::findOrCreate($role, 'web');
                if (in_array($role, ['admin', 'propriétaire'], true)) {
                    $roleModel->syncPermissions($permissions);
                }
            }
        }
    }
}
