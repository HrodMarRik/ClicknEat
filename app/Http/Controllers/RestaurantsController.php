<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorerestaurantsRequest;
use App\Http\Requests\UpdaterestaurantsRequest;
use App\Models\restaurants;

class RestaurantsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('restaurants.index', ['restaurants' => restaurants::all()]);
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
    public function store(StorerestaurantsRequest $request)
    {
        restaurants::create($request->all());
        return redirect()->route('restaurants.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(restaurants $restaurants)
    {
        return view('restaurants.show', ['restaurants' => $restaurants]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(restaurants $restaurants)
    {
        return view('restaurants.edit', ['restaurants' => $restaurants]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdaterestaurantsRequest $request, restaurants $restaurants)
    {
        $restaurants->update($request->all());
        return redirect()->route('restaurants.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(restaurants $restaurants)
    {
        $restaurants->delete();
        return redirect()->route('restaurants.index');
    }
}
