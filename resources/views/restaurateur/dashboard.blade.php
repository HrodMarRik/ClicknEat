<x-restaurateur-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord restaurateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienvenue, {{ Auth::user()->name }} !</h3>

                    @if($restaurants->isEmpty())
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Vous n'avez pas encore de restaurant. Commencez par en créer un !
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('restaurateur.restaurant.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Créer mon restaurant
                            </a>
                        </div>
                    @else
                        @foreach($restaurants as $restaurant)
                            <div class="mb-8 p-6 bg-white rounded-lg shadow-md">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-xl font-semibold text-gray-800">{{ $restaurant->name }}</h4>
                                    <span class="px-3 py-1 text-sm {{ $restaurant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full">
                                        {{ $restaurant->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                                    <div class="bg-indigo-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-indigo-600 mb-2">Total des plats</h5>
                                        <p class="text-2xl font-bold text-indigo-900">{{ $restaurantsStats[$restaurant->id]['totalDishes'] }}</p>
                                        <p class="text-sm text-indigo-500">Disponibles : {{ $restaurantsStats[$restaurant->id]['availableDishes'] }}</p>
                                    </div>

                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-green-600 mb-2">Total des commandes</h5>
                                        <p class="text-2xl font-bold text-green-900">{{ $restaurantsStats[$restaurant->id]['totalOrders'] }}</p>
                                        <p class="text-sm text-green-500">En attente : {{ $restaurantsStats[$restaurant->id]['pendingOrders'] }}</p>
                                    </div>

                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-blue-600 mb-2">Chiffre d'affaires</h5>
                                        <p class="text-2xl font-bold text-blue-900">{{ number_format($restaurantsStats[$restaurant->id]['totalRevenue'], 2) }} €</p>
                                    </div>

                                    <div class="bg-purple-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-purple-600 mb-2">Plats disponibles</h5>
                                        <p class="text-2xl font-bold text-purple-900">{{ $restaurantsStats[$restaurant->id]['availableDishes'] }}</p>
                                        <p class="text-sm text-purple-500">sur {{ $restaurantsStats[$restaurant->id]['totalDishes'] }}</p>
                                    </div>

                                    <div class="bg-yellow-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-yellow-600 mb-2">Commandes en attente</h5>
                                        <p class="text-2xl font-bold text-yellow-900">{{ $restaurantsStats[$restaurant->id]['pendingOrders'] }}</p>
                                    </div>
                                </div>

                                <div class="flex space-x-4">
                                    <a href="{{ route('restaurateur.restaurant.edit', $restaurant) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <span class="mr-2">→</span>Gérer ce restaurant
                                    </a>
                                    <a href="{{ route('restaurateur.dishes.index', ['restaurant' => $restaurant->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <span class="mr-2">→</span>Gérer les plats
                                    </a>
                                    <a href="{{ route('restaurateur.orders.index', ['restaurant' => $restaurant->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <span class="mr-2">→</span>Voir les commandes
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-restaurateur-layout>
