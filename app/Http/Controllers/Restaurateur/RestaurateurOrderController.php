<?php

namespace App\Http\Controllers\Restaurateur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurateurOrderController extends Controller
{
    public function index()
    {
        $restaurantIds = Restaurant::where('user_id', Auth::id())->pluck('id');

        $orders = Order::whereIn('restaurant_id', $restaurantIds)
                      ->with(['restaurant', 'user'])
                      ->latest()
                      ->paginate(15);

        return view('restaurateur.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Vérifier que la commande appartient à un restaurant du restaurateur connecté
        $restaurantIds = Restaurant::where('user_id', Auth::id())->pluck('id');

        if (!$restaurantIds->contains($order->restaurant_id)) {
            abort(403);
        }

        $order->load(['items.dish', 'restaurant', 'user']);

        return view('restaurateur.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Vérifier que la commande appartient à un restaurant du restaurateur connecté
        $restaurantIds = Restaurant::where('user_id', Auth::id())->pluck('id');

        if (!$restaurantIds->contains($order->restaurant_id)) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivering,delivered,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Le statut de la commande a été mis à jour.');
    }
}
