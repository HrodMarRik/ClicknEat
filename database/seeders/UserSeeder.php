<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Créer un restaurateur
        User::create([
            'name' => 'Restaurateur',
            'email' => 'restaurateur@example.com',
            'password' => Hash::make('password'),
            'role' => 'restaurateur',
            'email_verified_at' => now(),
        ]);

        // Créer un client
        User::create([
            'name' => 'Client',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        // Créer 10 clients supplémentaires
        User::factory()->count(10)->client()->create();

        // Créer 5 restaurateurs supplémentaires
        User::factory()->count(5)->restaurateur()->create();
    }
}
