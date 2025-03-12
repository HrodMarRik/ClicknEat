<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('client');
    }

    public function index()
    {
        $user = auth()->user();

        $stats = [
            'orders_count' => $user->orders->count(),
            'total_spent' => $user->orders->where('is_paid', true)->sum('total_price'),
            'recent_orders' => $user->orders()->with('restaurant')->latest()->take(5)->get(),
        ];

        return view('client.dashboard', compact('stats'));
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->with('restaurant')->latest()->paginate(10);
        return view('client.orders.index', compact('orders'));
    }

    public function profile()
    {
        return view('client.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès!');
    }
}
