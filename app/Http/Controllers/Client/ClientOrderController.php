<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientOrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
                      ->with('restaurant')
                      ->latest()
                      ->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Vérifier que la commande appartient au client connecté
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items', 'restaurant']);

        return view('client.orders.show', compact('order'));
    }
}
