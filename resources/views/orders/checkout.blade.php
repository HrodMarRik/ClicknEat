@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Paiement de la commande #{{ $order->id }}</h2>
            <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800">
                &larr; Retour à la commande
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Récapitulatif de la commande</h3>

                <div class="mb-4">
                    <p class="text-gray-600 mb-1">Restaurant: {{ $order->restaurant->name }}</p>
                    <p class="text-gray-600 mb-1">Date: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p class="text-gray-600 mb-1">Adresse de livraison: {{ $order->address }}</p>
                    <p class="text-gray-600 mb-4">Total à payer: <span class="font-semibold">{{ number_format($order->total, 2) }} €</span></p>
                </div>

                <div class="overflow-x-auto mb-6">
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
                                        <div class="text-sm text-gray-900">{{ $item->pivot->quantity }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($item->price * $item->pivot->quantity, 2) }} €</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h4 class="text-md font-medium text-gray-800 mb-4">Informations de paiement</h4>

                    <form action="{{ route('orders.process-payment', $order) }}" method="POST" id="payment-form">
                        @csrf

                        <div class="mb-4">
                            <label for="card-holder-name" class="block text-gray-700 text-sm font-bold mb-2">Nom sur la carte</label>
                            <input type="text" id="card-holder-name" name="card_holder_name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="mb-4">
                            <label for="card-number" class="block text-gray-700 text-sm font-bold mb-2">Numéro de carte</label>
                            <input type="text" id="card-number" name="card_number" required placeholder="1234 5678 9012 3456" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="card-expiry" class="block text-gray-700 text-sm font-bold mb-2">Date d'expiration (MM/AA)</label>
                                <input type="text" id="card-expiry" name="card_expiry" required placeholder="MM/AA" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div>
                                <label for="card-cvc" class="block text-gray-700 text-sm font-bold mb-2">CVC</label>
                                <input type="text" id="card-cvc" name="card_cvc" required placeholder="123" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800">
                                Annuler
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Payer {{ number_format($order->total, 2) }} €
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-4 text-center text-sm text-gray-500">
                    <p>Ceci est une simulation de paiement. Aucune carte réelle ne sera débitée.</p>
                    <p>Pour tester, utilisez le numéro de carte 4242 4242 4242 4242 avec n'importe quelle date future et n'importe quel CVC.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
