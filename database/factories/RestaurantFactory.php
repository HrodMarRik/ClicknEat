<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        $cuisines = ['Française', 'Italienne', 'Japonaise', 'Chinoise', 'Mexicaine', 'Indienne', 'Thaïlandaise', 'Américaine', 'Libanaise', 'Végétarienne'];

        return [
            'user_id' => User::factory()->restaurateur(),
            'name' => $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'cuisine' => $this->faker->randomElement($cuisines),
            'delivery_fee' => $this->faker->randomFloat(2, 0, 5),
            'opening_time' => '09:00',
            'closing_time' => '22:00',
            'is_active' => true,
            'image' => null,
        ];
    }

    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}
