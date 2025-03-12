<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Affiche le tableau de bord administrateur
     */
    public function index()
    {
        // Statistiques
        $totalUsers = User::count();
        $totalRestaurants = Restaurant::count();
        $totalOrders = Order::count();

        // Commandes récentes
        $recentOrders = Order::with(['user', 'restaurant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalRestaurants',
            'totalOrders',
            'recentOrders'
        ));
    }

    public function users()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function restaurants()
    {
        $restaurants = Restaurant::with('user')->latest()->paginate(15);
        return view('admin.restaurants.index', compact('restaurants'));
    }

    public function orders()
    {
        $orders = Order::with(['user', 'restaurant'])->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,restaurateur,client',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur mis à jour avec succès!');
    }

    public function deleteUser(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur supprimé avec succès!');
    }
}
