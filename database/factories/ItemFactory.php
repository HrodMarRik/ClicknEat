<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'cost' => $this->faker->randomNumber(),
            'price' => $this->faker->randomNumber(),
            'is_active' => $this->faker->boolean(),
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id,
        ];
    }
}
