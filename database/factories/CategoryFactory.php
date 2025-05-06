<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $categories = ['Entrées', 'Plats principaux', 'Desserts', 'Boissons', 'Spécialités', 'Végétarien', 'Promotions'];

        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => $this->faker->randomElement($categories),
            'description' => $this->faker->sentence(),
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
