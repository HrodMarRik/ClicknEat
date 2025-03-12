<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        // Initialiser Faker
        $faker = Faker::create('fr_FR');

        // Récupérer tous les utilisateurs restaurateurs
        $restaurateurs = User::where('role', 'restaurateur')->get();

        // Créer un restaurant pour chaque restaurateur
        foreach ($restaurateurs as $restaurateur) {
            // Créer manuellement pour éviter les problèmes de colonnes manquantes
            Restaurant::create([
                'user_id' => $restaurateur->id,
                'name' => $faker->company(),
                'description' => $faker->paragraph(),
                'address' => $faker->address(),
                'city' => $faker->city(),
                'postal_code' => $faker->postcode(),
                'phone' => $faker->phoneNumber(),
                'email' => $faker->companyEmail(),
                'cuisine' => $faker->randomElement(['Française', 'Italienne', 'Japonaise', 'Chinoise', 'Mexicaine']),
                'delivery_fee' => $faker->randomFloat(2, 0, 5),
                'min_order_amount' => $faker->randomFloat(2, 10, 20),
                'opening_hours' => json_encode([
                    'monday' => ['09:00-22:00'],
                    'tuesday' => ['09:00-22:00'],
                    'wednesday' => ['09:00-22:00'],
                    'thursday' => ['09:00-22:00'],
                    'friday' => ['09:00-23:00'],
                    'saturday' => ['10:00-23:00'],
                    'sunday' => ['10:00-22:00'],
                ]),
                'opening_time' => '09:00',
                'closing_time' => '22:00',
                'is_active' => true,
            ]);
        }

        // Créer 3 restaurants supplémentaires inactifs
        for ($i = 0; $i < 3; $i++) {
            $restaurateur = User::where('role', 'restaurateur')->inRandomOrder()->first();

            Restaurant::create([
                'user_id' => $restaurateur->id,
                'name' => $faker->company(),
                'description' => $faker->paragraph(),
                'address' => $faker->address(),
                'city' => $faker->city(),
                'postal_code' => $faker->postcode(),
                'phone' => $faker->phoneNumber(),
                'email' => $faker->companyEmail(),
                'cuisine' => $faker->randomElement(['Française', 'Italienne', 'Japonaise', 'Chinoise', 'Mexicaine']),
                'delivery_fee' => $faker->randomFloat(2, 0, 5),
                'min_order_amount' => $faker->randomFloat(2, 10, 20),
                'opening_hours' => json_encode([
                    'monday' => ['09:00-22:00'],
                    'tuesday' => ['09:00-22:00'],
                    'wednesday' => ['09:00-22:00'],
                    'thursday' => ['09:00-22:00'],
                    'friday' => ['09:00-23:00'],
                    'saturday' => ['10:00-23:00'],
                    'sunday' => ['10:00-22:00'],
                ]),
                'opening_time' => '09:00',
                'closing_time' => '22:00',
                'is_active' => false,
            ]);
        }
    }
}
