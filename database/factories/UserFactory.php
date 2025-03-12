<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // password
            'remember_token' => Str::random(10),
            'role' => $this->faker->randomElement(['client', 'restaurateur', 'admin']),
            'address' => $this->faker->optional()->address(),
            'city' => $this->faker->optional()->city(),
            'postal_code' => $this->faker->optional()->postcode(),
            'phone' => $this->faker->optional()->phoneNumber(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function client(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'client',
            ];
        });
    }

    public function restaurateur(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'restaurateur',
            ];
        });
    }

    public function admin(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
            ];
        });
    }
}
