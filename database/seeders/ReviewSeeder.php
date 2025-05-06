<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Récupérer toutes les commandes livrées
        $deliveredOrders = Order::where('status', 'delivered')->get();

        // Pour chaque commande livrée, il y a 70% de chance qu'un avis soit laissé
        foreach ($deliveredOrders as $order) {
            if (rand(1, 10) <= 7) {
                Review::create([
                    'user_id' => $order->user_id,
                    'restaurant_id' => $order->restaurant_id,
                    'order_id' => $order->id,
                    'rating' => rand(3, 5), // La plupart des avis sont positifs
                    'comment' => $this->getRandomComment($faker),
                ]);
            }
        }
    }

    private function getRandomComment($faker): string
    {
        $comments = [
            'Excellent service et nourriture délicieuse !',
            'Livraison rapide, plats encore chauds à l\'arrivée.',
            'Très bon rapport qualité-prix, je recommande.',
            'Les portions sont généreuses et le goût est au rendez-vous.',
            'Service impeccable, je commanderai à nouveau.',
            'Un peu d\'attente mais la qualité en vaut la peine.',
            'Parfait pour un repas en famille.',
            'Les plats sont fidèles à la description, très satisfait.',
            'Emballage soigné et livraison dans les temps.',
            'Une valeur sûre pour se faire plaisir.',
        ];

        return $comments[array_rand($comments)];
    }
}
