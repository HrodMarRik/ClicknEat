<x-restaurateur-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mes restaurants') }}
            </h2>
            <a href="{{ route('restaurateur.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
                &larr; Retour au tableau de bord
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="mb-6">
                <a href="{{ route('restaurateur.restaurant.create') }}" class="btn-primary">
                    Ajouter un restaurant
                </a>
            </div>

            @if($restaurants->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($restaurants as $restaurant)
                        <div class="card">
                            <div class="relative">
                                @if($restaurant->image)
                                    <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">Aucune image</span>
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2">
                                    <span class="badge {{ $restaurant->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $restaurant->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $restaurant->name }}</h3>
                                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($restaurant->description, 100) }}</p>

                                <!-- Statistiques -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="text-center">
                                        <span class="block text-2xl font-bold text-indigo-600">{{ $restaurant->dishes_count }}</span>
                                        <span class="text-sm text-gray-500">Plats</span>
                                    </div>
                                    <div class="text-center">
                                        <span class="block text-2xl font-bold text-indigo-600">{{ $restaurant->orders_count }}</span>
                                        <span class="text-sm text-gray-500">Commandes</span>
                                    </div>
                                </div>

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
                    <div class="p-6 bg-white border-b border-gray-200">
                        <p class="text-gray-500 text-center">Vous n'avez pas encore de restaurant.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-restaurateur-layout>
