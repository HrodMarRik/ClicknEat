<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Statistiques générales
        $stats = [
            'users' => User::count(),
            'restaurants' => Restaurant::count(),
            'orders' => Order::count(),
            'revenue' => Order::where('status', 'delivered')->sum('total'),
        ];

        // Commandes récentes avec possibilité de modification
        $recentOrders = Order::with(['user', 'restaurant'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Statuts pour l'affichage et la modification
        $statuses = [
            'pending' => 'En attente',
            'preparing' => 'En préparation',
            'ready' => 'Prêt',
            'delivering' => 'En livraison',
            'delivered' => 'Livré',
            'cancelled' => 'Annulé',
        ];

        // Restaurants les plus populaires
        $popularRestaurants = Restaurant::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        // Utilisateurs les plus actifs
        $activeUsers = User::where('role', 'client')
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        // Revenus par mois (pour le graphique)
        $revenueByMonth = Order::where('status', 'delivered')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('SUM(total) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get()
            ->reverse();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'statuses',
            'popularRestaurants',
            'activeUsers',
            'revenueByMonth'
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
