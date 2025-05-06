<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Vérifier si la colonne special_instructions existe
        $hasSpecialInstructions = Schema::hasColumn('order_items', 'special_instructions');
        $hasDishName = Schema::hasColumn('order_items', 'dish_name');
        $hasSubtotal = Schema::hasColumn('order_items', 'subtotal');

        // Récupérer toutes les commandes
        $orders = Order::all();

        foreach ($orders as $order) {
            // Récupérer les plats du restaurant associé à la commande
            $dishes = Dish::where('restaurant_id', $order->restaurant_id)
                        ->where('is_available', true)
                        ->inRandomOrder()
                        ->take(rand(1, 5))
                        ->get();

            foreach ($dishes as $dish) {
                $quantity = rand(1, 3);
                $itemData = [
                    'order_id' => $order->id,
                    'dish_id' => $dish->id,
                    'quantity' => $quantity,
                    'price' => $dish->price,
                ];

                // Ajouter dish_name si la colonne existe
                if ($hasDishName) {
                    $itemData['dish_name'] = $dish->name;
                }

                // Ajouter subtotal si la colonne existe
                if ($hasSubtotal) {
                    $itemData['subtotal'] = $dish->price * $quantity;
                }

                // Ajouter special_instructions si la colonne existe
                if ($hasSpecialInstructions) {
                    $itemData['special_instructions'] = rand(0, 1) ? $faker->sentence() : null;
                }

                try {
                    OrderItem::create($itemData);
                } catch (\Exception $e) {
                    // Si une erreur se produit, afficher un message et continuer
                    echo "Erreur lors de la création d'un élément de commande : " . $e->getMessage() . "\n";
                    continue;
                }
            }
        }
    }
}
