<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Category;
use App\Models\Dish;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->authorizeResource(Restaurant::class, 'restaurant', [
            'except' => ['index', 'show']
        ]);
    }

    public function index(Request $request)
    {
        $query = Restaurant::where('is_active', true);

        if ($request->filled('cuisine')) {
            $query->where('cuisine_type', $request->cuisine);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $restaurants = $query->orderBy('name')->paginate(12);

        $cuisines = Restaurant::where('is_active', true)
                            ->whereNotNull('cuisine_type')
                            ->distinct()
                            ->pluck('cuisine_type')
                            ->sort();

        return view('restaurants.index', compact('restaurants', 'cuisines'));
    }

    public function create()
    {
        return view('restaurants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'horaires' => 'required|array',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('restaurants', 'public');
            $validated['logo'] = $path;
        }

        $restaurant = auth()->user()->restaurants()->create($validated);

        return redirect()->route('restaurants.show', $restaurant)
            ->with('success', 'Restaurant créé avec succès!');
    }

    public function show(Restaurant $restaurant)
    {
        if (!$restaurant->is_active) {
            abort(404);
        }

        $categories = Category::where('restaurant_id', $restaurant->id)
            ->orderBy('order')
            ->with(['dishes' => function($query) {
                $query->where('is_available', true);
            }])
            ->get();

        $reviews = Review::where('restaurant_id', $restaurant->id)
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->paginate(5);

        $averageRating = Review::where('restaurant_id', $restaurant->id)->avg('rating');
        $reviewsCount = Review::where('restaurant_id', $restaurant->id)->count();

        return view('restaurants.show', compact('restaurant', 'categories', 'reviews', 'averageRating', 'reviewsCount'));
    }

    public function edit(Restaurant $restaurant)
    {
        return view('restaurants.edit', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'horaires' => 'required|array',
        ]);

        if ($request->hasFile('logo')) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $path = $request->file('logo')->store('restaurants', 'public');
            $validated['logo'] = $path;
        }

        $restaurant->update($validated);

        return redirect()->route('restaurants.show', $restaurant)
            ->with('success', 'Restaurant mis à jour avec succès!');
    }

    public function destroy(Restaurant $restaurant)
    {
        if ($restaurant->logo) {
            Storage::disk('public')->delete($restaurant->logo);
        }

        $restaurant->delete();

        return redirect()->route('restaurants.index')
            ->with('success', 'Restaurant supprimé avec succès!');
    }
}
