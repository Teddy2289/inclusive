<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user - a toutes les permissions
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Assigner le rôle admin (qui a déjà toutes les permissions via RolesAndPermissionsSeeder)
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminUser->syncRoles($adminRole);
        }

        // Utilisateur Manager (optionnel - pour tester)
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Gestionnaire',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerUser->syncRoles($managerRole);
        }

        // Utilisateur Conseiller (optionnel - pour tester)
        $conseillerUser = User::firstOrCreate(
            ['email' => 'conseiller@example.com'],
            [
                'name' => 'Conseiller',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $conseillerRole = Role::where('name', 'conseiller')->first();
        if ($conseillerRole) {
            $conseillerUser->syncRoles($conseillerRole);
        }

        // Utilisateur Admin supplémentaire (si besoin)
        $secondAdmin = User::firstOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Second Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        if ($adminRole) {
            $secondAdmin->syncRoles($adminRole);
        }
    }
}
