@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Créer un nouveau restaurant</h2>
            <a href="{{ route('restaurateur.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
                &larr; Retour au tableau de bord
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('restaurants.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom du restaurant</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('name')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" id="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Adresse</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('address')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Téléphone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('phone')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="cuisine" class="block text-gray-700 text-sm font-bold mb-2">Type de cuisine</label>
                        <select name="cuisine" id="cuisine" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Sélectionnez un type de cuisine</option>
                            <option value="française" {{ old('cuisine') == 'française' ? 'selected' : '' }}>Française</option>
                            <option value="italienne" {{ old('cuisine') == 'italienne' ? 'selected' : '' }}>Italienne</option>
                            <option value="japonaise" {{ old('cuisine') == 'japonaise' ? 'selected' : '' }}>Japonaise</option>
                            <option value="chinoise" {{ old('cuisine') == 'chinoise' ? 'selected' : '' }}>Chinoise</option>
                            <option value="indienne" {{ old('cuisine') == 'indienne' ? 'selected' : '' }}>Indienne</option>
                            <option value="mexicaine" {{ old('cuisine') == 'mexicaine' ? 'selected' : '' }}>Mexicaine</option>
                            <option value="libanaise" {{ old('cuisine') == 'libanaise' ? 'selected' : '' }}>Libanaise</option>
                            <option value="américaine" {{ old('cuisine') == 'américaine' ? 'selected' : '' }}>Américaine</option>
                        </select>
                        @error('cuisine')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image</label>
                        <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('image')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Créer le restaurant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
