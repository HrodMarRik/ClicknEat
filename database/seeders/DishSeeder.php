<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Dish;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DishSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Pour chaque catégorie
        Category::all()->each(function ($category) use ($faker) {
            // Créer entre 3 et 8 plats par catégorie
            $dishCount = rand(3, 8);

            for ($i = 0; $i < $dishCount; $i++) {
                Dish::create([
                    'restaurant_id' => $category->restaurant_id,
                    'category_id' => $category->id,
                    'name' => $faker->words(3, true),
                    'description' => $faker->paragraph(),
                    'price' => $faker->randomFloat(2, 5, 30),
                    'image' => null,
                    'is_available' => true,
                ]);
            }
        });
    }
}
