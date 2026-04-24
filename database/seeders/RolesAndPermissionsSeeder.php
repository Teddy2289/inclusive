<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // Définir les permissions
        $permissions = [
            'partenaires.view',
            'partenaires.create',
            'partenaires.edit',
            'partenaires.delete',
            'partenaires.import',
            'contacts.view',
            'contacts.create',
            'contacts.edit',
            'contacts.delete',
            'users.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Rôle Admin : tout
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Rôle Manager : tout sauf gestion users
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'partenaires.view', 'partenaires.create',
            'partenaires.edit', 'partenaires.import',
            'contacts.view', 'contacts.create', 'contacts.edit',
        ]);

        // Rôle Conseiller : lecture seule
        $conseiller = Role::firstOrCreate(['name' => 'conseiller']);
        $conseiller->syncPermissions([
            'partenaires.view',
            'contacts.view',
        ]);
    }
}
