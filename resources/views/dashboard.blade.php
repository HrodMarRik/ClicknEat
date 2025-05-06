@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Tableau de bord</h2>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Bienvenue, {{ auth()->user()->name }} !</h3>
            <p class="text-gray-600">Voici un résumé de votre activité sur notre plateforme.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-semibold mb-4">Vos informations</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Nom :</span> {{ auth()->user()->name }}</p>
                    <p><span class="font-medium">Email :</span> {{ auth()->user()->email }}</p>
                    <p><span class="font-medium">Adresse :</span> {{ auth()->user()->address ?: 'Non renseignée' }}</p>
                    <p><span class="font-medium">Ville :</span> {{ auth()->user()->city ?: 'Non renseignée' }}</p>
                    <p><span class="font-medium">Code postal :</span> {{ auth()->user()->postal_code ?: 'Non renseigné' }}</p>
                    <p><span class="font-medium">Téléphone :</span> {{ auth()->user()->phone ?: 'Non renseigné' }}</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Modifier mon profil
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-semibold mb-4">Statistiques</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Nombre total de commandes :</span> {{ $totalOrders }}</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Voir toutes mes commandes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Commandes récentes</h3>

            @if($recentOrders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->restaurant->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($order->total, 2) }} €</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($order->status == 'delivered') bg-green-100 text-green-800
                                            @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status == 'preparing') bg-blue-100 text-blue-800
                                            @elseif($order->status == 'delivering') bg-purple-100 text-purple-800
                                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">Détails</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-gray-500">Vous n'avez pas encore passé de commande.</p>
                    <a href="{{ route('restaurants.index') }}" class="inline-flex items-center px-4 py-2 mt-4 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Découvrir les restaurants
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
