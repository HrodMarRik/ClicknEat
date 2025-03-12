@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Mes restaurants</h2>
        <a href="{{ route('restaurateur.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
            &larr; Retour au tableau de bord
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-6">
        <a href="{{ route('restaurateur.restaurant.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            Ajouter un restaurant
        </a>
    </div>

    @if($restaurants->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($restaurants as $restaurant)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="relative">
                        @if($restaurant->image)
                            <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">Aucune image</span>
                            </div>
                        @endif
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $restaurant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $restaurant->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $restaurant->name }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($restaurant->description, 100) }}</p>
                        <div class="flex justify-between items-center">
                            <a href="{{ route('restaurateur.restaurant.edit', $restaurant) }}" class="text-indigo-600 hover:text-indigo-800">
                                Modifier
                            </a>
                            <a href="{{ route('restaurants.show', $restaurant) }}" class="text-gray-600 hover:text-gray-800" target="_blank">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 text-center">
                <p class="text-gray-500">Vous n'avez pas encore de restaurant.</p>
                <p class="text-gray-500 mt-2">Commencez par en créer un !</p>
            </div>
        </div>
    @endif
</div>
@endsection
