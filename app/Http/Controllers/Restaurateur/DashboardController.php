<?php

namespace App\Http\Controllers\Restaurateur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\Dish;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('restaurateur');
    }

    /**
     * Affiche le tableau de bord du restaurateur
     */
    public function index()
    {
        // Récupérer tous les restaurants du restaurateur connecté avec leurs statistiques
        $restaurants = Restaurant::where('user_id', auth()->id())
            ->withCount(['dishes', 'orders'])
            ->get();

        $restaurantsStats = [];

        foreach ($restaurants as $restaurant) {
            $restaurantsStats[$restaurant->id] = $this->getRestaurantStats($restaurant);
        }

        // Débogage
        Log::info('Dashboard data:', [
            'restaurants' => $restaurants->toArray(),
            'restaurantsStats' => $restaurantsStats
        ]);

        return view('restaurateur.dashboard', [
            'restaurants' => $restaurants,
            'restaurantsStats' => $restaurantsStats
        ]);
    }

    /**
     * Récupère les statistiques du restaurant
     */
    private function getRestaurantStats(Restaurant $restaurant)
    {
        // Statistiques de base
        $stats = [
            'totalDishes' => Dish::where('restaurant_id', $restaurant->id)->count(),
            'availableDishes' => Dish::where('restaurant_id', $restaurant->id)
                ->where('is_available', true)
                ->count(),
            'totalOrders' => Order::where('restaurant_id', $restaurant->id)->count(),
            'pendingOrders' => Order::where('restaurant_id', $restaurant->id)
                ->whereIn('status', ['pending', 'preparing', 'ready'])
                ->count(),
            'totalRevenue' => Order::where('restaurant_id', $restaurant->id)
                ->where('status', 'delivered')
                ->where('payment_status', 'paid')
                ->sum('total')
        ];

        return $stats;
    }

    public function restaurants()
    {
        $restaurants = auth()->user()->restaurants()->latest()->paginate(10);
        return view('restaurateur.restaurants.index', compact('restaurants'));
    }

    public function orders()
    {
        $user = auth()->user();
        $restaurantIds = $user->restaurants->pluck('id');

        $orders = Order::whereIn('restaurant_id', $restaurantIds)
            ->with(['user', 'restaurant'])
            ->latest()
            ->paginate(15);

        return view('restaurateur.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $this->authorize('updateStatus', $order);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $order->update($validated);

        return back()->with('success', 'Statut de la commande mis à jour avec succès!');
    }
}
