<?php

namespace App\Http\Controllers\Restaurateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Restaurant;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes du restaurant
     */
    public function index()
    {
        $restaurant = $this->getRestaurant();

        if (!$restaurant) {
            return redirect()->route('restaurateur.dashboard')
                ->with('error', 'Vous devez d\'abord créer un restaurant.');
        }

        $orders = Order::where('restaurant_id', $restaurant->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('restaurateur.orders.index', compact('orders'));
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        $restaurant = $this->getRestaurant();

        if (!$restaurant || $order->restaurant_id !== $restaurant->id) {
            return redirect()->route('restaurateur.orders.index')
                ->with('error', 'Vous n\'êtes pas autorisé à voir cette commande.');
        }

        $order->load(['user', 'orderItems.dish']);

        return view('restaurateur.orders.show', compact('order'));
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function update(Request $request, Order $order)
    {
        $restaurant = $this->getRestaurant();

        if (!$restaurant || $order->restaurant_id !== $restaurant->id) {
            return redirect()->route('restaurateur.orders.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette commande.');
        }

        $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivering,delivered,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->route('restaurateur.orders.show', $order)
            ->with('success', 'Statut de la commande mis à jour avec succès.');
    }

    /**
     * Récupère le restaurant du restaurateur connecté
     */
    private function getRestaurant()
    {
        return Restaurant::where('user_id', auth()->id())->first();
    }
}
