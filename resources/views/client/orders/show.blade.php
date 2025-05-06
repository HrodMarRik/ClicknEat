@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Détails de la commande #{{ $order->id }}</h2>
            <a href="{{ route('client.orders.index') }}" class="text-indigo-600 hover:text-indigo-800">
                &larr; Retour aux commandes
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de commande</h3>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Restaurant</p>
                            <p class="font-medium">{{ $order->restaurant->name }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Date de commande</p>
                            <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Statut</p>
                            <p class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                            </p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Paiement</p>
                            <p class="font-medium">
                                @if($order->payment_status == 'paid')
                                    <span class="text-green-600">Payé</span>
                                @else
                                    <span class="text-yellow-600">En attente</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Adresse de livraison</h3>

                        <div class="mb-4">
                            <p class="font-medium">{{ Auth::user()->name }}</p>
                            <p>{{ $order->address }}</p>
                            <p>{{ $order->postal_code }} {{ $order->city }}</p>
                            <p>{{ $order->phone }}</p>
                        </div>

                        @if($order->notes)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Instructions de livraison</p>
                                <p>{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Détails de la commande</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Plat
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prix unitaire
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantité
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ number_format($item->price, 2) }} €</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ number_format($item->price * $item->quantity, 2) }} €</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 border-t pt-6">
                    <div class="flex justify-between text-base font-medium text-gray-900">
                        <p>Sous-total</p>
                        <p>{{ number_format($order->subtotal, 2) }} €</p>
                    </div>
                    <div class="flex justify-between text-base font-medium text-gray-900 mt-2">
                        <p>Frais de livraison</p>
                        <p>{{ number_format($order->delivery_fee, 2) }} €</p>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900 mt-4">
                        <p>Total</p>
                        <p>{{ number_format($order->total, 2) }} €</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
