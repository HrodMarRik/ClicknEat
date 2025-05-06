@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="text-center mb-6">
                    <svg class="mx-auto h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-800 mt-4">Commande confirmée!</h2>
                    <p class="text-gray-600 mt-2">Votre commande #{{ $order->id }} a été enregistrée avec succès.</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Détails de la commande</h3>
                    <p class="text-gray-600">Restaurant: {{ $order->restaurant->name }}</p>
                    <p class="text-gray-600">Date: {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                    <p class="text-gray-600">Statut:
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            En attente
                        </span>
                    </p>
                    <p class="text-gray-600">Paiement:
                        @if($order->is_paid)
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                Payé
                            </span>
                        @else
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                Non payé
                            </span>
                        @endif
                    </p>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Articles commandés</h3>
                    <div class="border rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Article
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
                                            <div class="text-sm text-gray-900">{{ number_format($item->pivot->price, 2) }} €</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->pivot->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ number_format($item->pivot->price * $item->pivot->quantity, 2) }} €</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Total</h3>
                    <p class="text-gray-600">Sous-total: {{ number_format($order->total_price - 2.99, 2) }} €</p>
                    <p class="text-gray-600">Frais de livraison: {{ number_format(2.99, 2) }} €</p>
                    <p class="font-bold text-indigo-600 text-lg">Total: {{ number_format($order->total_price, 2) }} €</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Informations de livraison</h3>
                    <p class="text-gray-600">Adresse: {{ $order->delivery_address }}</p>
                    <p class="text-gray-600">Téléphone: {{ $order->phone }}</p>
                    @if($order->notes)
                        <p class="text-gray-600">Notes: {{ $order->notes }}</p>
                    @endif
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('home') }}" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                        Retour à l'accueil
                    </a>

                    @if(!$order->is_paid)
                        <a href="{{ route('orders.checkout', $order) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Procéder au paiement
                        </a>
                    @else
                        <a href="{{ route('orders.show', $order) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Voir les détails
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection