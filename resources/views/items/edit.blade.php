@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Modifier le plat</h2>
            <a href="{{ route('restaurants.edit', $item->restaurant) }}" class="text-indigo-600 hover:text-indigo-800">
                &larr; Retour au menu du restaurant
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('items.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom du plat</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('name')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" id="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Prix (€)</label>
                        <input type="number" name="price" id="price" value="{{ old('price', $item->price) }}" step="0.01" min="0" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('price')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Catégorie</label>
                        <select name="category" id="category" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="entrée" {{ old('category', $item->category) == 'entrée' ? 'selected' : '' }}>Entrée</option>
                            <option value="plat" {{ old('category', $item->category) == 'plat' ? 'selected' : '' }}>Plat principal</option>
                            <option value="dessert" {{ old('category', $item->category) == 'dessert' ? 'selected' : '' }}>Dessert</option>
                            <option value="boisson" {{ old('category', $item->category) == 'boisson' ? 'selected' : '' }}>Boisson</option>
                        </select>
                        @error('category')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Mettre à jour le plat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
