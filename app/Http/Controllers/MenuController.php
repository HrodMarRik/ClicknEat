<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->authorizeResource(Menu::class, 'menu', [
            'except' => ['index', 'show']
        ]);
    }

    public function index(Restaurant $restaurant)
    {
        $menus = $restaurant->menus()->with('items')->get();
        return view('menus.index', compact('restaurant', 'menus'));
    }

    public function create(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        return view('menus.create', compact('restaurant'));
    }

    public function store(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $menu = $restaurant->menus()->create($validated);

        return redirect()->route('restaurants.menus.show', [$restaurant, $menu])
            ->with('success', 'Menu créé avec succès!');
    }

    public function show(Restaurant $restaurant, Menu $menu)
    {
        $menu->load('items');
        return view('menus.show', compact('restaurant', 'menu'));
    }

    public function edit(Restaurant $restaurant, Menu $menu)
    {
        return view('menus.edit', compact('restaurant', 'menu'));
    }

    public function update(Request $request, Restaurant $restaurant, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $menu->update($validated);

        return redirect()->route('restaurants.menus.show', [$restaurant, $menu])
            ->with('success', 'Menu mis à jour avec succès!');
    }

    public function destroy(Restaurant $restaurant, Menu $menu)
    {
        $menu->delete();

        return redirect()->route('restaurants.show', $restaurant)
            ->with('success', 'Menu supprimé avec succès!');
    }
}
