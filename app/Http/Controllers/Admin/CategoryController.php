<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Affiche le formulaire de création d'une catégorie
     */
    public function create(Request $request)
    {
        $restaurant = null;
        if ($request->has('restaurant_id')) {
            $restaurant = Restaurant::findOrFail($request->restaurant_id);
        }

        return view('admin.categories.create', compact('restaurant'));
    }

    /**
     * Enregistre une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'restaurant_id' => 'required|exists:restaurants,id',
        ]);

        $category = Category::create($validated);

        return redirect()->route('admin.restaurants.show', $category->restaurant_id)
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'une catégorie
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Met à jour une catégorie
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('admin.restaurants.show', $category->restaurant_id)
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprime une catégorie
     */
    public function destroy(Category $category)
    {
        $restaurantId = $category->restaurant_id;
        $category->delete();

        return redirect()->route('admin.restaurants.show', $restaurantId)
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}
