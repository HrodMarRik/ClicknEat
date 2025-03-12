@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Tableau de bord administrateur</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-2">Utilisateurs</h2>
            <p class="text-3xl font-bold">{{ $totalUsers }}</p>
            <a href="{{ route('admin.users.index') }}" class="text-blue-500 hover:underline mt-2 inline-block">Voir tous les utilisateurs</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-2">Restaurants</h2>
            <p class="text-3xl font-bold">{{ $totalRestaurants }}</p>
            <a href="{{ route('admin.restaurants.index') }}" class="text-blue-500 hover:underline mt-2 inline-block">Voir tous les restaurants</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-2">Commandes</h2>
            <p class="text-3xl font-bold">{{ $totalOrders }}</p>
            <a href="{{ route('admin.orders.index') }}" class="text-blue-500 hover:underline mt-2 inline-block">Voir toutes les commandes</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Commandes récentes</h2>

        @if($recentOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Client</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Restaurant</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-200">{{ $order->id }}</td>
                                <td class="py-2 px-4 border-b border-gray-200">{{ $order->user->name }}</td>
                                <td class="py-2 px-4 border-b border-gray-200">{{ $order->restaurant->name }}</td>
                                <td class="py-2 px-4 border-b border-gray-200">{{ number_format($order->total, 2) }} €</td>
                                <td class="py-2 px-4 border-b border-gray-200">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($order->status == 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-2 px-4 border-b border-gray-200">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-500 hover:underline">Détails</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">Aucune commande récente.</p>
        @endif

        <div class="mt-4">
            <a href="{{ route('admin.orders.index') }}" class="text-blue-500 hover:underline">Voir toutes les commandes</a>
        </div>
    </div>
</div>
@endsection
