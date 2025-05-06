<?php

namespace App\Http\Controllers\Restaurateur;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Dish;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RestaurateurDishController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        // Vérifier que le restaurant appartient au restaurateur connecté
        if ($restaurant->user_id !== Auth::id()) {
            abort(403);
        }

        $dishes = Dish::where('restaurant_id', $restaurant->id)
                      ->with('category')
                      ->orderBy('category_id')
                      ->orderBy('name')
                      ->paginate(15);

        return view('restaurateur.dishes.index', compact('restaurant', 'dishes'));
    }

    public function create(Restaurant $restaurant)
    {
        // Vérifier que le restaurant appartient au restaurateur connecté
        if ($restaurant->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::where('restaurant_id', $restaurant->id)
                             ->orderBy('order')
                             ->get();

        return view('restaurateur.dishes.create', compact('restaurant', 'categories'));
    }

    public function store(Request $request, Restaurant $restaurant)
    {
        // Vérifier que le restaurant appartient au restaurateur connecté
        if ($restaurant->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $dish = new Dish();
        $dish->restaurant_id = $restaurant->id;
        $dish->name = $request->name;
        $dish->description = $request->description;
        $dish->price = $request->price;
        $dish->category_id = $request->category_id;
        $dish->is_available = $request->has('is_available');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('dishes', 'public');
            $dish->image = $path;
        }

        $dish->save();

        return redirect()->route('restaurateur.dishes.index', $restaurant)
                         ->with('success', 'Le plat a été ajouté avec succès.');
    }

    public function edit(Restaurant $restaurant, Dish $dish)
    {
        // Vérifier que le restaurant appartient au restaurateur connecté
        if ($restaurant->user_id !== Auth::id()) {
            abort(403);
        }

        // Vérifier que le plat appartient au restaurant
        if ($dish->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $categories = Category::where('restaurant_id', $restaurant->id)
                             ->orderBy('order')
                             ->get();

        return view('restaurateur.dishes.edit', compact('restaurant', 'dish', 'categories'));
    }

    public function update(Request $request, Restaurant $restaurant, Dish $dish)
    {
        // Vérifier que le restaurant appartient au restaurateur connecté
        if ($restaurant->user_id !== Auth::id()) {
            abort(403);
        }

        // Vérifier que le plat appartient au restaurant
        if ($dish->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $dish->name = $request->name;
        $dish->description = $request->description;
        $dish->price = $request->price;
        $dish->category_id = $request->category_id;
        $dish->is_available = $request->has('is_available');

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($dish->image) {
                Storage::disk('public')->delete($dish->image);
            }

            $path = $request->file('image')->store('dishes', 'public');
            $dish->image = $path;
        }

        $dish->save();

        return redirect()->route('restaurateur.dishes.index', $restaurant)
                         ->with('success', 'Le plat a été mis à jour avec succès.');
    }

    public function destroy(Restaurant $restaurant, Dish $dish)
    {
        // Vérifier que le restaurant appartient au restaurateur connecté
        if ($restaurant->user_id !== Auth::id()) {
            abort(403);
        }

        // Vérifier que le plat appartient au restaurant
        if ($dish->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        // Supprimer l'image si elle existe
        if ($dish->image) {
            Storage::disk('public')->delete($dish->image);
        }

        $dish->delete();

        return redirect()->route('restaurateur.dishes.index', $restaurant)
                         ->with('success', 'Le plat a été supprimé avec succès.');
    }
}
