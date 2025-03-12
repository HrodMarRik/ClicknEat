<?php

namespace App\Http\Controllers\Restaurateur;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RestaurateurRestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::where('user_id', Auth::id())
                                ->withCount('dishes')
                                ->withCount('orders')
                                ->withAvg('reviews', 'rating')
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('restaurateur.restaurants.index', compact('restaurants'));
    }

    public function create()
    {
        return view('restaurateur.restaurants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'cuisine' => 'required|string|max:100',
            'delivery_fee' => 'required|numeric|min:0',
            'delivery_time' => 'nullable|integer|min:0',
            'opening_hours' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $restaurant = new Restaurant();
        $restaurant->user_id = Auth::id();
        $restaurant->name = $request->name;
        $restaurant->description = $request->description;
        $restaurant->address = $request->address;
        $restaurant->city = $request->city;
        $restaurant->postal_code = $request->postal_code;
        $restaurant->phone = $request->phone;
        $restaurant->email = $request->email;
        $restaurant->cuisine = $request->cuisine;
        $restaurant->delivery_fee = $request->delivery_fee;
        $restaurant->delivery_time = $request->delivery_time;
        $restaurant->opening_hours = $request->opening_hours;
        $restaurant->is_active = false; // Par défaut, le restaurant est inactif jusqu'à validation par l'admin

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('restaurants', 'public');
            $restaurant->image = $path;
        }

        $restaurant->save();

        return redirect()->route('restaurateur.restaurants.index')
                         ->with('success', 'Le restaurant a été créé avec succès et est en attente de validation.');
    }

    public function edit(Restaurant $restaurant)
    {
        // Vérifier que le restaurant appartient au restaurateur connecté
        if ($restaurant->user_id !== Auth::id()) {
            abort(403);
        }

        return view('restaurateur.restaurants.edit', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        // Vérifier que le restaurant appartient au restaurateur connecté
        if ($restaurant->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'cuisine' => 'required|string|max:100',
            'delivery_fee' => 'required|numeric|min:0',
            'delivery_time' => 'nullable|integer|min:0',
            'opening_hours' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $restaurant->name = $request->name;
        $restaurant->description = $request->description;
        $restaurant->address = $request->address;
        $restaurant->city = $request->city;
        $restaurant->postal_code = $request->postal_code;
        $restaurant->phone = $request->phone;
        $restaurant->email = $request->email;
        $restaurant->cuisine = $request->cuisine;
        $restaurant->delivery_fee = $request->delivery_fee;
        $restaurant->delivery_time = $request->delivery_time;
        $restaurant->opening_hours = $request->opening_hours;

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($restaurant->image) {
                Storage::disk('public')->delete($restaurant->image);
            }

            $path = $request->file('image')->store('restaurants', 'public');
            $restaurant->image = $path;
        }

        $restaurant->save();

        return redirect()->route('restaurateur.restaurants.index')
                         ->with('success', 'Le restaurant a été mis à jour avec succès.');
    }
}
