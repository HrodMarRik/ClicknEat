<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil
     */
    public function index()
    {
        // Récupérer les restaurants les mieux notés
        $topRatedRestaurants = Restaurant::where('is_active', true)
            ->select('restaurants.*', DB::raw('(SELECT AVG(reviews.rating) FROM reviews WHERE restaurants.id = reviews.restaurant_id) as reviews_avg_rating'))
            ->orderBy('reviews_avg_rating', 'desc')
            ->limit(6)
            ->get();

        // Récupérer les nouveaux restaurants
        $newRestaurants = Restaurant::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Calculer la note moyenne pour chaque nouveau restaurant
        $newRestaurants->each(function ($restaurant) {
            $restaurant->averageRating = $restaurant->reviews()->avg('rating');
        });

        return view('home', compact('topRatedRestaurants', 'newRestaurants'));
    }
}
