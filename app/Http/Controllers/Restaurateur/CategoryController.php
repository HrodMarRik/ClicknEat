<?php

namespace App\Http\Controllers\Restaurateur;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories
     */
    public function index()
    {
        // Récupérer les restaurants du restaurateur
        $restaurants = auth()->user()->restaurants;

        if ($restaurants->isEmpty()) {
            return redirect()->route('restaurateur.restaurant.create')
                ->with('warning', 'Vous devez d\'abord créer un restaurant avant de pouvoir gérer des catégories.');
        }

        // Récupérer les catégories de tous les restaurants du restaurateur
        $categories = Category::whereIn('restaurant_id', $restaurants->pluck('id'))
                            ->with('restaurant')
                            ->orderBy('restaurant_id')
                            ->orderBy('order')
                            ->get();

        return view('restaurateur.categories.index', compact('categories', 'restaurants'));
    }

    /**
     * Affiche le formulaire de création d'une catégorie
     */
    public function create()
    {
        $restaurants = auth()->user()->restaurants;

        if ($restaurants->isEmpty()) {
            return redirect()->route('restaurateur.restaurant.create')
                ->with('warning', 'Vous devez d\'abord créer un restaurant avant de pouvoir ajouter des catégories.');
        }

        return view('restaurateur.categories.create', compact('restaurants'));
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

        // Vérifier que le restaurant appartient au restaurateur connecté
        $restaurant = Restaurant::findOrFail($validated['restaurant_id']);
        if ($restaurant->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à ajouter des catégories à ce restaurant.');
        }

        // Déterminer l'ordre de la nouvelle catégorie
        $maxOrder = Category::where('restaurant_id', $validated['restaurant_id'])->max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;

        Category::create($validated);

        return redirect()->route('restaurateur.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'une catégorie
     */
    public function edit(Category $category)
    {
        // Vérifier que la catégorie appartient à un restaurant du restaurateur connecté
        $restaurant = $category->restaurant;
        if ($restaurant->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette catégorie.');
        }

        $restaurants = auth()->user()->restaurants;

        return view('restaurateur.categories.edit', compact('category', 'restaurants'));
    }

    /**
     * Met à jour une catégorie
     */
    public function update(Request $request, Category $category)
    {
        // Vérifier que la catégorie appartient à un restaurant du restaurateur connecté
        $restaurant = $category->restaurant;
        if ($restaurant->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette catégorie.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'restaurant_id' => 'required|exists:restaurants,id',
        ]);

        // Vérifier que le nouveau restaurant appartient au restaurateur connecté
        $newRestaurant = Restaurant::findOrFail($validated['restaurant_id']);
        if ($newRestaurant->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à déplacer cette catégorie vers ce restaurant.');
        }

        // Si le restaurant a changé, ajuster l'ordre
        if ($category->restaurant_id != $validated['restaurant_id']) {
            $maxOrder = Category::where('restaurant_id', $validated['restaurant_id'])->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        $category->update($validated);

        return redirect()->route('restaurateur.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprime une catégorie
     */
    public function destroy(Category $category)
    {
        // Vérifier que la catégorie appartient à un restaurant du restaurateur connecté
        $restaurant = $category->restaurant;
        if ($restaurant->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette catégorie.');
        }

        // Vérifier si la catégorie contient des plats
        if ($category->dishes()->count() > 0) {
            return redirect()->route('restaurateur.categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des plats. Veuillez d\'abord supprimer ou déplacer les plats.');
        }

        $category->delete();

        // Réorganiser les ordres des catégories restantes
        $categories = Category::where('restaurant_id', $restaurant->id)
                            ->orderBy('order')
                            ->get();

        foreach ($categories as $index => $cat) {
            $cat->update(['order' => $index + 1]);
        }

        return redirect()->route('restaurateur.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }

    /**
     * Réorganise les catégories
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        foreach ($validated['categories'] as $index => $categoryId) {
            $category = Category::findOrFail($categoryId);

            // Vérifier que la catégorie appartient à un restaurant du restaurateur connecté
            $restaurant = $category->restaurant;
            if ($restaurant->user_id !== auth()->id()) {
                abort(403, 'Vous n\'êtes pas autorisé à réorganiser cette catégorie.');
            }

            $category->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
