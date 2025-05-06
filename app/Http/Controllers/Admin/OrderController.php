<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes
     */
    public function index(Request $request)
    {
        // Pour les administrateurs, récupérer toutes les commandes sans restriction d'utilisateur
        $query = Order::with(['user', 'restaurant']);

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('restaurant', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtrage par utilisateur (seulement si spécifié explicitement)
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filtrage par restaurant
        if ($request->has('restaurant_id') && $request->restaurant_id) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        // Filtrage par statut
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filtrage par date
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        // Récupérer les statuts pour le filtre
        $statuses = [
            'pending' => 'En attente',
            'preparing' => 'En préparation',
            'ready' => 'Prêt',
            'delivering' => 'En livraison',
            'delivered' => 'Livré',
            'cancelled' => 'Annulé',
        ];

        // Récupérer les utilisateurs pour le filtre
        $users = User::where('role', 'client')->orderBy('name')->get();

        // Récupérer les restaurants pour le filtre
        $restaurants = Restaurant::orderBy('name')->get();

        return view('admin.orders.index', compact('orders', 'statuses', 'users', 'restaurants'));
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        $order->load(['user', 'restaurant', 'items.dish']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Affiche le formulaire d'édition d'une commande
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'restaurant', 'items.dish']);
        $statuses = [
            'pending' => 'En attente',
            'preparing' => 'En préparation',
            'ready' => 'Prêt',
            'delivering' => 'En livraison',
            'delivered' => 'Livré',
            'cancelled' => 'Annulé',
        ];
        return view('admin.orders.edit', compact('order', 'statuses'));
    }

    /**
     * Met à jour une commande
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivering,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Commande mise à jour avec succès.');
    }

    /**
     * Supprime une commande
     */
    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Commande supprimée avec succès.');
    }

    /**
     * Met à jour rapidement le statut d'une commande
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivering,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès.');
    }
}
