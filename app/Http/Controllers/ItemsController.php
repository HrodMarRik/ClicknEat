<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreitemsRequest;
use App\Http\Requests\UpdateitemsRequest;
use App\Models\Items;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('items.index', ['items' => Items::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreitemsRequest $request)
    {
        Items::create($request->all());
        return redirect()->route('items.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Items $items)
    {
        return view('items.show', ['items' => $items]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Items $items)
    {
        return view('items.edit', ['items' => $items]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateitemsRequest $request, Items $items)
    {
        $items->update($request->all());
        return redirect()->route('items.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Items $items)
    {
        $items->delete();
        return redirect()->route('items.index');
    }
}
