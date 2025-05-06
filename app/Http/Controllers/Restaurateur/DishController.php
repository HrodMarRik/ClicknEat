<?php

namespace App\Http\Controllers\Restaurateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class DishController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:restaurateur');
    }

    /**
     * Récupère le restaurant du restaurateur connecté
     */
    private function getRestaurant()
    {
        $restaurant = Restaurant::where('user_id', auth()->id())->first();

        if (!$restaurant) {
            return null;
        }

        return $restaurant;
    }

    /**
     * Vérifie si le restaurateur a un restaurant
     */
    private function checkRestaurant()
    {
        $restaurant = $this->getRestaurant();

        if (!$restaurant) {
            return redirect()->route('restaurateur.dashboard')
                ->with('error', 'Vous devez d\'abord créer un restaurant.');
        }

        return $restaurant;
    }

    /**
     * Affiche la liste des plats
     */
    public function index()
    {
        $restaurant = $this->checkRestaurant();

        if (!$restaurant instanceof Restaurant) {
            return $restaurant; // C'est une redirection
        }

        $dishes = Dish::where('restaurant_id', $restaurant->id)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('restaurateur.dishes.index', compact('dishes'));
    }

    /**
     * Affiche le formulaire de création d'un plat
     */
    public function create()
    {
        $restaurant = $this->checkRestaurant();

        if (!$restaurant instanceof Restaurant) {
            return $restaurant; // C'est une redirection
        }

        // Essayons de récupérer les catégories associées au restaurant
        $categories = Category::where('restaurant_id', $restaurant->id)->get();

        // Si aucune catégorie n'est trouvée, récupérons toutes les catégories
        if ($categories->isEmpty()) {
            $categories = Category::all();
        }

        return view('restaurateur.dishes.create', compact('categories'));
    }

    /**
     * Enregistre un nouveau plat
     */
    public function store(Request $request)
    {
        $restaurant = $this->checkRestaurant();

        if (!$restaurant instanceof Restaurant) {
            return $restaurant; // C'est une redirection
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'nullable|boolean',
        ]);

        $dish = new Dish();
        $dish->restaurant_id = $restaurant->id;
        $dish->name = $validated['name'];
        $dish->description = $validated['description'];
        $dish->price = $validated['price'];
        $dish->category_id = $validated['category_id'];
        $dish->is_available = $request->has('is_available');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('dishes', 'public');
            $dish->image = $path;
        }

        $dish->save();

        return redirect()->route('restaurateur.dishes.index')
            ->with('success', 'Plat ajouté avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un plat
     */
    public function edit(Dish $dish)
    {
        $restaurant = $this->getRestaurant();

        if (!$restaurant || $dish->restaurant_id !== $restaurant->id) {
            return redirect()->route('restaurateur.dishes.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce plat.');
        }

        $categories = Category::orderBy('name')->get();

        return view('restaurateur.dishes.edit', compact('dish', 'categories'));
    }

    /**
     * Met à jour un plat
     */
    public function update(Request $request, Dish $dish)
    {
        $restaurant = $this->getRestaurant();

        if (!$restaurant || $dish->restaurant_id !== $restaurant->id) {
            return redirect()->route('restaurateur.dishes.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce plat.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'nullable|boolean',
        ]);

        $dish->name = $validated['name'];
        $dish->description = $validated['description'];
        $dish->price = $validated['price'];
        $dish->category_id = $validated['category_id'];
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

        return redirect()->route('restaurateur.dishes.index')
            ->with('success', 'Plat mis à jour avec succès.');
    }

    /**
     * Supprime un plat
     */
    public function destroy(Dish $dish)
    {
        $restaurant = $this->getRestaurant();

        if (!$restaurant || $dish->restaurant_id !== $restaurant->id) {
            return redirect()->route('restaurateur.dishes.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer ce plat.');
        }

        // Supprimer l'image si elle existe
        if ($dish->image) {
            Storage::disk('public')->delete($dish->image);
        }

        $dish->delete();

        return redirect()->route('restaurateur.dishes.index')
            ->with('success', 'Plat supprimé avec succès.');
    }
}
