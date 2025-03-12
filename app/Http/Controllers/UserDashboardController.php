<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class UserDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'utilisateur
     */
    public function index()
    {
        // Récupérer les commandes récentes de l'utilisateur
        $recentOrders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->with('restaurant')
            ->limit(5)
            ->get();

        // Compter le nombre total de commandes
        $totalOrders = Order::where('user_id', auth()->id())->count();

        return view('dashboard', compact('recentOrders', 'totalOrders'));
    }
}
