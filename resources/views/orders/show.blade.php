<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la commande #' . $order->id) }}
            </h2>
            <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-800">
                &larr; Retour à mes commandes
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Informations de la commande -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de la commande</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Restaurant</p>
                                    <p class="font-medium">{{ $order->restaurant->name }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600">Date de commande</p>
                                    <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>

                                <div>
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

                                <div>
                                    <p class="text-sm text-gray-600">Paiement</p>
                                    <p class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->is_paid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $order->is_paid ? 'Payé' : 'Non payé' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Articles commandés -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Articles commandés</h3>

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
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $item->dish->name }}</div>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($order->subtotal, 2) }} €</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">Frais de livraison</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($order->delivery_fee, 2) }} €</td>
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

                <!-- Informations de livraison -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de livraison</h3>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600">Adresse de livraison</p>
                                    <p class="font-medium">{{ $order->delivery_address }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600">Code postal</p>
                                    <p class="font-medium">{{ $order->delivery_postal_code }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600">Ville</p>
                                    <p class="font-medium">{{ $order->delivery_city }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600">Téléphone</p>
                                    <p class="font-medium">{{ $order->delivery_phone }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600">Instructions de livraison</p>
                                    <p class="font-medium">{{ $order->delivery_instructions ?: 'Aucune instruction' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
