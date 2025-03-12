<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    /**
     * Affiche la liste des restaurants
     */
    public function index(Request $request)
    {
        $query = Restaurant::with('user');

        // Filtrage par cuisine
        if ($request->filled('cuisine')) {
            $query->where('cuisine_type', $request->cuisine);
        }

        // Filtrage par statut
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Recherche par nom ou adresse
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $restaurants = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.restaurants.index', compact('restaurants'));
    }

    /**
     * Affiche le formulaire de création d'un restaurant
     */
    public function create()
    {
        $restaurateurs = User::where('role', 'restaurateur')->get();
        return view('admin.restaurants.create', compact('restaurateurs'));
    }

    /**
     * Enregistre un nouveau restaurant
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'delivery_fee' => 'required|numeric|min:0',
            'minimum_order' => 'required|numeric|min:0',
            'delivery_time' => 'required|integer|min:0',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant = Restaurant::create($data);

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant créé avec succès.');
    }

    /**
     * Affiche les détails d'un restaurant
     */
    public function show(Restaurant $restaurant)
    {
        $restaurant->load(['user', 'dishes', 'categories']);
        return view('admin.restaurants.show', compact('restaurant'));
    }

    /**
     * Affiche le formulaire d'édition d'un restaurant
     */
    public function edit(Restaurant $restaurant)
    {
        $restaurateurs = User::where('role', 'restaurateur')->get();
        return view('admin.restaurants.edit', compact('restaurant', 'restaurateurs'));
    }

    /**
     * Met à jour un restaurant
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        // Si la requête ne contient qu'un champ is_active, c'est une activation/désactivation rapide
        if ($request->has('is_active') && count($request->all()) === 3) { // 3 car il y a aussi _token et _method
            $restaurant->update([
                'is_active' => $request->is_active
            ]);

            $status = $request->is_active ? 'activé' : 'désactivé';
            return redirect()->route('admin.restaurants.index')
                ->with('success', "Restaurant {$status} avec succès.");
        }

        // Sinon, c'est une mise à jour complète
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'delivery_fee' => 'required|numeric|min:0',
            'minimum_order' => 'required|numeric|min:0',
            'delivery_time' => 'required|integer|min:0',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($restaurant->image) {
                Storage::disk('public')->delete($restaurant->image);
            }

            $data['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant->update($data);

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant mis à jour avec succès.');
    }

    /**
     * Supprime un restaurant
     */
    public function destroy(Restaurant $restaurant)
    {
        // Supprimer l'image si elle existe
        if ($restaurant->image) {
            Storage::disk('public')->delete($restaurant->image);
        }

        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant supprimé avec succès.');
    }

    /**
     * Activate the specified restaurant.
     */
    public function activate(Restaurant $restaurant)
    {
        $restaurant->update(['is_active' => true]);

        return redirect()->back()
            ->with('success', 'Restaurant activé avec succès.');
    }

    /**
     * Deactivate the specified restaurant.
     */
    public function deactivate(Restaurant $restaurant)
    {
        $restaurant->update(['is_active' => false]);

        return redirect()->back()
            ->with('success', 'Restaurant désactivé avec succès.');
    }
}
