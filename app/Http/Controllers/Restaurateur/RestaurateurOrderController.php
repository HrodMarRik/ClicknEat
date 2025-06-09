<?php

namespace App\Http\Controllers\Restaurateur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurateurOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:restaurateur');
    }

    /**
     * Affiche la liste des commandes du restaurant
     */
    public function index(Request $request)
    {
        $restaurant = Restaurant::where('user_id', Auth::id())->first();

        if (!$restaurant) {
            return redirect()->route('restaurateur.dashboard')
                ->with('error', 'Vous devez d\'abord créer un restaurant.');
        }

        $query = Order::where('restaurant_id', $restaurant->id)
            ->with(['user', 'items'])
            ->latest();

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->paginate(10);

        return view('restaurateur.orders.index', compact('orders'));
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        // Vérifier que la commande appartient au restaurant du restaurateur
        $restaurant = Restaurant::where('user_id', Auth::id())->first();

        if (!$restaurant || $order->restaurant_id !== $restaurant->id) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette commande.');
        }

        $order->load(['user', 'items']);

        return view('restaurateur.orders.show', compact('order'));
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function update(Request $request, Order $order)
    {
        // Vérifier que la commande appartient au restaurant du restaurateur
        $restaurant = Restaurant::where('user_id', Auth::id())->first();

        if (!$restaurant || $order->restaurant_id !== $restaurant->id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette commande.');
        }

        $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivered,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        // Envoyer une notification au client
        // TODO: Implémenter les notifications

        return redirect()->back()->with('success', 'Le statut de la commande a été mis à jour.');
    }
}
