@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Tableau de bord client</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Commandes récentes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Mes commandes récentes</h3>
                        <a href="{{ route('client.orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Voir toutes</a>
                    </div>

                    @if(isset($recentOrders) && $recentOrders->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentOrders as $order)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Commande #{{ $order->id }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                            <div class="text-sm text-gray-700 mt-1">
                                                {{ $order->restaurant->name }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($order->status == 'preparing') bg-blue-100 text-blue-800
                                                @elseif($order->status == 'ready') bg-indigo-100 text-indigo-800
                                                @elseif($order->status == 'delivering') bg-purple-100 text-purple-800
                                                @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                                @endif">
                                                @if($order->status == 'pending') En attente
                                                @elseif($order->status == 'preparing') En préparation
                                                @elseif($order->status == 'ready') Prêt
                                                @elseif($order->status == 'delivering') En livraison
                                                @elseif($order->status == 'delivered') Livré
                                                @elseif($order->status == 'cancelled') Annulé
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('client.orders.show', $order) }}" class="text-sm text-indigo-600 hover:text-indigo-800">Voir les détails</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Vous n'avez pas encore de commandes.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Restaurants favoris -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Restaurants populaires</h3>
                        <a href="{{ route('restaurants.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Voir tous</a>
                    </div>

                    @if(isset($popularRestaurants) && $popularRestaurants->count() > 0)
                        <div class="space-y-4">
                            @foreach($popularRestaurants->take(3) as $restaurant)
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($restaurant->image)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($restaurant->image) }}" alt="{{ $restaurant->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $restaurant->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $restaurant->cuisine }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('restaurants.show', $restaurant) }}" class="text-sm text-indigo-600 hover:text-indigo-800">Voir le menu</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucun restaurant disponible pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profil -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Mon profil</h3>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Modifier</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Nom</div>
                        <div class="mt-1 text-sm text-gray-900">{{ auth()->user()->name }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Email</div>
                        <div class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Téléphone</div>
                        <div class="mt-1 text-sm text-gray-900">{{ auth()->user()->phone ?: 'Non renseigné' }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Adresse</div>
                        <div class="mt-1 text-sm text-gray-900">{{ auth()->user()->address ?: 'Non renseignée' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
