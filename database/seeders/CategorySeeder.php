<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        $categories = ['Entrées', 'Plats principaux', 'Desserts', 'Boissons', 'Spécialités'];

        // Pour chaque restaurant actif
        Restaurant::where('is_active', true)->get()->each(function ($restaurant) use ($categories, $faker) {
            // Créer les catégories standard
            foreach ($categories as $index => $categoryName) {
                Category::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => $categoryName,
                    'description' => 'Nos délicieux ' . strtolower($categoryName),
                    'order' => $index + 1,
                ]);
            }
        });
    }
}
