<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->authorizeResource(Item::class, 'item', [
            'except' => ['index', 'show']
        ]);
    }

    public function index(Restaurant $restaurant, Menu $menu)
    {
        $items = $menu->items()->paginate(12);
        return view('items.index', compact('restaurant', 'menu', 'items'));
    }

    public function create(Restaurant $restaurant, Menu $menu)
    {
        $this->authorize('update', $menu);
        $categories = ['Entrée', 'Plat principal', 'Dessert', 'Boisson', 'Accompagnement'];
        return view('items.create', compact('restaurant', 'menu', 'categories'));
    }

    public function store(Request $request, Restaurant $restaurant, Menu $menu)
    {
        $this->authorize('update', $menu);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'available' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $validated['image'] = $path;
        }

        $item = $menu->items()->create($validated);

        return redirect()->route('restaurants.menus.items.show', [$restaurant, $menu, $item])
            ->with('success', 'Item créé avec succès!');
    }

    public function show(Restaurant $restaurant, Menu $menu, Item $item)
    {
        return view('items.show', compact('restaurant', 'menu', 'item'));
    }

    public function edit(Restaurant $restaurant, Menu $menu, Item $item)
    {
        $categories = ['Entrée', 'Plat principal', 'Dessert', 'Boisson', 'Accompagnement'];
        return view('items.edit', compact('restaurant', 'menu', 'item', 'categories'));
    }

    public function update(Request $request, Restaurant $restaurant, Menu $menu, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'available' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $path = $request->file('image')->store('items', 'public');
            $validated['image'] = $path;
        }

        $item->update($validated);

        return redirect()->route('restaurants.menus.items.show', [$restaurant, $menu, $item])
            ->with('success', 'Item mis à jour avec succès!');
    }

    public function destroy(Restaurant $restaurant, Menu $menu, Item $item)
    {
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return redirect()->route('restaurants.menus.show', [$restaurant, $menu])
            ->with('success', 'Item supprimé avec succès!');
    }
}
