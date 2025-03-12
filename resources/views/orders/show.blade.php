@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Détails de la commande #{{ $order->id }}</h2>
        <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-800">
            &larr; Retour à mes commandes
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Informations de la commande -->
        <div class="md:col-span-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations de la commande</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Statut</p>
                            <p class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($order->status == 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'preparing') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'delivering') bg-purple-100 text-purple-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Date de commande</p>
                            <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Restaurant</p>
                            <p class="font-medium">{{ $order->restaurant->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Adresse de livraison</p>
                            <p class="font-medium">{{ $order->delivery_address }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Téléphone</p>
                            <p class="font-medium">{{ $order->phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Instructions de livraison</p>
                            <p class="font-medium">{{ $order->delivery_instructions ?: 'Aucune' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Articles de la commande -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Articles commandés</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item->dish->name }}
                                            @if($item->options)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @foreach(json_decode($item->options) as $key => $value)
                                                        <div>{{ ucfirst($key) }}: {{ is_array($value) ? implode(', ', $value) : $value }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item->price, 2) }} €</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item->price * $item->quantity, 2) }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">Sous-total</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($order->subtotal, 2) }} €</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">Frais de livraison</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($order->delivery_fee, 2) }} €</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">Total</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($order->total, 2) }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suivi de commande -->
        <div class="md:col-span-1">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Suivi de commande</h3>

                    <div class="relative">
                        <div class="absolute left-4 top-0 h-full w-0.5 bg-gray-200"></div>

                        <div class="relative flex items-start mb-6">
                            <div class="flex items-center h-6">
                                <div class="relative z-10 w-6 h-6 flex items-center justify-center bg-{{ $order->status == 'pending' || $order->status == 'preparing' || $order->status == 'delivering' || $order->status == 'delivered' ? 'green' : 'gray' }}-500 rounded-full">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">Commande reçue</h4>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="relative flex items-start mb-6">
                            <div class="flex items-center h-6">
                                <div class="relative z-10 w-6 h-6 flex items-center justify-center bg-{{ $order->status == 'preparing' || $order->status == 'delivering' || $order->status == 'delivered' ? 'green' : 'gray' }}-500 rounded-full">
                                    @if($order->status == 'preparing' || $order->status == 'delivering' || $order->status == 'delivered')
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">En préparation</h4>
                                <p class="text-xs text-gray-500">Votre commande est en cours de préparation</p>
                            </div>
                        </div>

                        <div class="relative flex items-start mb-6">
                            <div class="flex items-center h-6">
                                <div class="relative z-10 w-6 h-6 flex items-center justify-center bg-{{ $order->status == 'delivering' || $order->status == 'delivered' ? 'green' : 'gray' }}-500 rounded-full">
                                    @if($order->status == 'delivering' || $order->status == 'delivered')
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">En livraison</h4>
                                <p class="text-xs text-gray-500">Votre commande est en route</p>
                            </div>
                        </div>

                        <div class="relative flex items-start">
                            <div class="flex items-center h-6">
                                <div class="relative z-10 w-6 h-6 flex items-center justify-center bg-{{ $order->status == 'delivered' ? 'green' : 'gray' }}-500 rounded-full">
                                    @if($order->status == 'delivered')
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">Livrée</h4>
                                <p class="text-xs text-gray-500">Votre commande a été livrée</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
