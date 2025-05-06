@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Gestion des commandes</h2>
            <a href="{{ route('restaurateur.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
                &larr; Retour au tableau de bord
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('restaurateur.orders') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div>
                        <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                            <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>En préparation</option>
                            <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Prête</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livrée</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>
                    <div>
                        <select name="restaurant" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Tous les restaurants</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" {{ request('restaurant') == $restaurant->id ? 'selected' : '' }}>{{ $restaurant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="sort" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récentes</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Plus anciennes</option>
                            <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Montant le plus élevé</option>
                            <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Montant le plus bas</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Restaurant
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Client
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Paiement
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $order->restaurant->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $order->created_at->format('d/m/Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($order->total, 2) }} €</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status == 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $order->status == 'preparing' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                            {{ $order->status == 'ready' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                        ">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->is_paid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $order->is_paid ? 'Payée' : 'Non payée' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Détails
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Aucune commande trouvée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
