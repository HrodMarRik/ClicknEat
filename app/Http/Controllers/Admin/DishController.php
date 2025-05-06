<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Http\Request;

class DishController extends Controller
{
    /**
     * Affiche le formulaire de création d'un plat
     */
    public function create(Request $request)
    {
        $restaurant = null;
        if ($request->has('restaurant_id')) {
            $restaurant = Restaurant::findOrFail($request->restaurant_id);
            $categories = Category::where('restaurant_id', $restaurant->id)->get();
        } else {
            $categories = collect();
        }

        return view('admin.dishes.create', compact('restaurant', 'categories'));
    }

    /**
     * Enregistre un nouveau plat
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'restaurant_id' => 'required|exists:restaurants,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $dish = Dish::create($validated);

        return redirect()->route('admin.restaurants.show', $dish->restaurant_id)
            ->with('success', 'Plat créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un plat
     */
    public function edit(Dish $dish)
    {
        $categories = Category::where('restaurant_id', $dish->restaurant_id)->get();
        return view('admin.dishes.edit', compact('dish', 'categories'));
    }

    /**
     * Met à jour un plat
     */
    public function update(Request $request, Dish $dish)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $dish->update($validated);

        return redirect()->route('admin.restaurants.show', $dish->restaurant_id)
            ->with('success', 'Plat mis à jour avec succès.');
    }

    /**
     * Supprime un plat
     */
    public function destroy(Dish $dish)
    {
        $restaurantId = $dish->restaurant_id;
        $dish->delete();

        return redirect()->route('admin.restaurants.show', $restaurantId)
            ->with('success', 'Plat supprimé avec succès.');
    }
}
