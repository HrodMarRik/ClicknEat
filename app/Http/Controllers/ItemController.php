<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('items.index', ['items' => Item::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create', ['categories' => Category::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        Item::create($request->validated());
        return redirect()->route('items.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return view('items.show', ['item' => $item]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        return view('items.edit', ['item' => $item, 'categories' => Category::all()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update($request->validated());
        return redirect()->route('items.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index');
    }
}
