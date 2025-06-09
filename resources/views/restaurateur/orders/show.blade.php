<x-restaurateur-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la commande #' . $order->id) }}
            </h2>
            <a href="{{ route('restaurateur.orders.index') }}" class="text-indigo-600 hover:text-indigo-800">
                &larr; Retour aux commandes
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Informations de la commande -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de la commande</h3>
                            <dl class="grid grid-cols-1 gap-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                    <dd class="text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status === 'preparing' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $order->status === 'ready' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                            {{ $order->status === 'delivering' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Total</dt>
                                    <dd class="text-sm text-gray-900">{{ number_format($order->total, 2) }} €</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Mode de paiement</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($order->payment_method) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Statut du paiement</dt>
                                    <dd class="text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $order->payment_status === 'paid' ? 'Payé' : 'En attente' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du client</h3>
                            <dl class="grid grid-cols-1 gap-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Nom</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->user->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->user->email }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->phone }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Adresse de livraison</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->delivery_address }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Articles de la commande -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Articles commandés</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plat</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->dish->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item->unit_price, 2) }} €</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item->unit_price * $item->quantity, 2) }} €</td>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format($order->total, 2) }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Actions -->
                    @if($order->status === 'pending')
                        <div class="mt-6 flex justify-end space-x-4">
                            <form action="{{ route('restaurateur.orders.update', $order) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="preparing">
                                <button type="submit" class="btn-primary">Accepter la commande</button>
                            </form>
                            <form action="{{ route('restaurateur.orders.update', $order) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">Refuser la commande</button>
                            </form>
                        </div>
                    @elseif($order->status === 'preparing')
                        <div class="mt-6 flex justify-end">
                            <form action="{{ route('restaurateur.orders.update', $order) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="ready">
                                <button type="submit" class="btn-primary">Marquer comme prêt</button>
                            </form>
                        </div>
                    @elseif($order->status === 'ready')
                        <div class="mt-6 flex justify-end">
                            <form action="{{ route('restaurateur.orders.update', $order) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="delivering">
                                <button type="submit" class="btn-primary">En livraison</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-restaurateur-layout>
