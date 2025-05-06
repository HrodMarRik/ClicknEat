<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DishFactory extends Factory
{
    protected $model = Dish::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'category_id' => Category::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 5, 30),
            'image' => null,
            'is_available' => true,
        ];
    }

    public function unavailable(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_available' => false,
            ];
        });
    }
}
