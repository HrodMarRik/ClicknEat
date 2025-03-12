<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    public function index()
    {
        // Récupérer les commandes récentes du client
        $recentOrders = Order::where('user_id', Auth::id())
                            ->with('restaurant')
                            ->latest()
                            ->take(3)
                            ->get();

        // Récupérer les restaurants populaires
        $popularRestaurants = Restaurant::where('is_active', true)
                                ->withAvg('reviews', 'rating')
                                ->orderByDesc('reviews_avg_rating')
                                ->take(3)
                                ->get();

        return view('client.dashboard', compact('recentOrders', 'popularRestaurants'));
    }
}
