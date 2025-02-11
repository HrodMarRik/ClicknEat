<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('restaurants.index', ['restaurants' => Restaurant::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('restaurants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestaurantRequest $request)
    {
        Restaurant::create($request->all());
        return redirect()->route('restaurants.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        return view('restaurants.show', ['restaurant' => $restaurant]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restaurant $restaurant)
    {
        return view('restaurants.edit', ['restaurant' => $restaurant]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
    {
        $restaurant->update($request->all());
        return redirect()->route('restaurants.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();
        return redirect()->route('restaurants.index');
    }
}
