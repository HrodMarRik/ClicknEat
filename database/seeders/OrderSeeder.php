<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Récupérer tous les clients
        $clients = User::where('role', 'client')->get();

        // Récupérer tous les restaurants actifs
        $restaurants = Restaurant::where('is_active', true)->get();

        // Pour chaque client
        foreach ($clients as $client) {
            // Créer entre 0 et 5 commandes
            $orderCount = rand(0, 5);

            for ($i = 0; $i < $orderCount; $i++) {
                // Choisir un restaurant au hasard
                $restaurant = $restaurants->random();

                // Récupérer les plats de ce restaurant
                $dishes = Dish::where('restaurant_id', $restaurant->id)
                            ->where('is_available', true)
                            ->inRandomOrder()
                            ->take(rand(1, 5))
                            ->get();

                if ($dishes->count() > 0) {
                    // Calculer le sous-total
                    $subtotal = 0;
                    foreach ($dishes as $dish) {
                        $quantity = rand(1, 3);
                        $subtotal += $dish->price * $quantity;
                    }

                    // Frais de livraison
                    $deliveryFee = $restaurant->delivery_fee;

                    // Total
                    $total = $subtotal + $deliveryFee;

                    // Données de base pour la commande
                    $orderData = [
                        'user_id' => $client->id,
                        'restaurant_id' => $restaurant->id,
                        'status' => rand(0, 5) > 4 ? 'pending' : 'delivered',
                        'subtotal' => $subtotal,
                        'delivery_fee' => $deliveryFee,
                        'total' => $total,
                        'address' => $client->address ?? $faker->address(),
                        'city' => $client->city ?? $faker->city(),
                        'postal_code' => $client->postal_code ?? $faker->postcode(),
                        'phone' => $client->phone ?? $faker->phoneNumber(),
                        'notes' => rand(0, 1) ? 'Merci de livrer rapidement.' : null,
                        'payment_status' => 'paid',
                    ];

                    // Ajouter payment_method si la colonne existe
                    if (Schema::hasColumn('orders', 'payment_method')) {
                        $orderData['payment_method'] = rand(0, 1) ? 'card' : 'cash';
                    }

                    // Ajouter payment_intent_id si la colonne existe
                    if (Schema::hasColumn('orders', 'payment_intent_id')) {
                        $orderData['payment_intent_id'] = rand(0, 1) ? 'pi_' . $faker->md5() : null;
                    }

                    try {
                        $order = Order::create($orderData);

                        // Créer les éléments de commande
                        foreach ($dishes as $dish) {
                            $quantity = rand(1, 3);

                            $itemData = [
                                'order_id' => $order->id,
                                'dish_id' => $dish->id,
                                'quantity' => $quantity,
                                'price' => $dish->price,
                            ];

                            // Ajouter special_instructions si la colonne existe
                            if (Schema::hasColumn('order_items', 'special_instructions')) {
                                $itemData['special_instructions'] = rand(0, 1) ? $faker->sentence() : null;
                            }

                            OrderItem::create($itemData);
                        }
                    } catch (\Exception $e) {
                        // Si une erreur se produit, afficher un message et continuer
                        echo "Erreur lors de la création d'une commande : " . $e->getMessage() . "\n";
                        continue;
                    }
                }
            }
        }
    }
}
