<x-app-layout>
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

                    @if(!$restaurant)
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
                        <p class="mt-1 text-sm text-gray-600">
                            Votre restaurant : {{ $restaurant->name }}
                        </p>

                        <div class="mt-4">
                            <h4 class="font-medium text-gray-700">Statistiques :</h4>
                            <ul class="list-disc pl-5 mt-2">
                                <li>Nombre total de plats : {{ $stats['totalDishes'] ?? 0 }}</li>
                                <li>Plats disponibles : {{ $stats['availableDishes'] ?? 0 }}</li>
                                <li>Nombre total de commandes : {{ $stats['totalOrders'] ?? 0 }}</li>
                                <li>Commandes en attente : {{ $stats['pendingOrders'] ?? 0 }}</li>
                                <li>Chiffre d'affaires total : {{ number_format($stats['totalRevenue'] ?? 0, 2) }} €</li>
                            </ul>
                        </div>

                        <div class="mt-4 flex space-x-4">
                            <a href="{{ route('restaurateur.restaurant.edit', $restaurant) }}" class="text-indigo-600 hover:text-indigo-900">Gérer mon restaurant</a>
                            <a href="{{ route('restaurateur.dishes.index') }}" class="text-indigo-600 hover:text-indigo-900">Gérer mes plats</a>
                            <a href="{{ route('restaurateur.orders.index') }}" class="text-indigo-600 hover:text-indigo-900">Voir les commandes</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
