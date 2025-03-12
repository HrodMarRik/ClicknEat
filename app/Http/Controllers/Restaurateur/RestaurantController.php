<?php

namespace App\Http\Controllers\Restaurateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    /**
     * Affiche la liste des restaurants du restaurateur
     */
    public function index()
    {
        $restaurants = auth()->user()->restaurants()->latest()->get();

        return view('restaurateur.restaurants.index', compact('restaurants'));
    }

    /**
     * Affiche le formulaire de création d'un restaurant
     */
    public function create()
    {
        return view('restaurateur.restaurants.create');
    }

    /**
     * Enregistre un nouveau restaurant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'cuisine_type' => 'required|string|max:100',
            'opening_hours' => 'required|json',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['is_active'] = true;

        $restaurant = Restaurant::create($validated);

        return redirect()->route('restaurateur.restaurant.index')
            ->with('success', 'Restaurant créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un restaurant
     */
    public function edit(Restaurant $restaurant)
    {
        // Vérifier que le restaurant appartient au restaurateur connecté
        if ($restaurant->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce restaurant.');
        }

        return view('restaurateur.restaurants.edit', compact('restaurant'));
    }

    /**
     * Met à jour un restaurant
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        // Vérifier que le restaurant appartient au restaurateur connecté
        if ($restaurant->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce restaurant.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'cuisine_type' => 'required|string|max:100',
            'opening_hours' => 'required|json',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($restaurant->image) {
                Storage::disk('public')->delete($restaurant->image);
            }

            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant->update($validated);

        return redirect()->route('restaurateur.restaurant.index')
            ->with('success', 'Restaurant mis à jour avec succès.');
    }
}
