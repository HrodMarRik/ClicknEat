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
        // Trouver tous les utilisateurs avec le rôle 'restaurateur'
        $restaurateurs = User::where('role', 'restaurateur')->get();

        foreach ($restaurateurs as $restaurateur) {
            // Créer 1 à 3 restaurants pour chaque restaurateur
            $numRestaurants = rand(1, 3);

            for ($i = 0; $i < $numRestaurants; $i++) {
                Restaurant::create([
                    'user_id' => $restaurateur->id,
                    'name' => fake()->company(),
                    'description' => fake()->paragraph(3),
                    'address' => fake()->streetAddress(),
                    'city' => fake()->city(),
                    'postal_code' => fake()->postcode(),
                    'phone' => fake()->phoneNumber(),
                    'email' => fake()->email(),
                    'cuisine' => fake()->randomElement(['Française', 'Italienne', 'Japonaise', 'Chinoise', 'Mexicaine', 'Indienne', 'Thaïlandaise']),
                    'min_order_amount' => fake()->randomFloat(2, 0, 15),
                    'delivery_fee' => fake()->randomFloat(2, 2, 5),
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
                    'accepts_onsite_orders' => true,
                    'preparation_time' => fake()->numberBetween(15, 45),
                ]);
            }
        }
    }
}
