<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la commande #') . $order->id }}
            </h2>
            <a href="{{ route('restaurateur.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Retour aux commandes
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de la commande</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600"><span class="font-medium">Client :</span> {{ $order->user->name }}</p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Email :</span> {{ $order->user->email }}</p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Téléphone :</span> {{ $order->phone ?? 'Non spécifié' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600"><span class="font-medium">Date :</span> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Adresse :</span> {{ $order->address ?? 'Non spécifiée' }}</p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Ville :</span> {{ $order->city ?? 'Non spécifiée' }}, {{ $order->postal_code ?? '' }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="font-medium text-gray-700 mb-2">Statut de la commande</h4>
                        <div class="flex items-center">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if ($order->status == 'delivered')
                                    bg-green-100 text-green-800
                                @elseif ($order->status == 'cancelled')
                                    bg-red-100 text-red-800
                                @elseif ($order->status == 'pending')
                                    bg-yellow-100 text-yellow-800
                                @elseif ($order->status == 'preparing')
                                    bg-blue-100 text-blue-800
                                @elseif ($order->status == 'ready')
                                    bg-purple-100 text-purple-800
                                @elseif ($order->status == 'delivering')
                                    bg-indigo-100 text-indigo-800
                                @endif
                            ">
                                {{ ucfirst($order->status) }}
                            </span>

                            <form action="{{ route('restaurateur.orders.update', $order) }}" method="POST" class="ml-4">
                                @csrf
                                @method('PUT')
                                <div class="flex items-center">
                                    <select name="status" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>En préparation</option>
                                        <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Prête</option>
                                        <option value="delivering" {{ $order->status == 'delivering' ? 'selected' : '' }}>En livraison</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Livrée</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                    </select>
                                    <button type="submit" class="ml-2 inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        Mettre à jour
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="font-medium text-gray-700 mb-2">Informations de paiement</h4>
                        <p class="text-sm text-gray-600"><span class="font-medium">Statut du paiement :</span>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if ($order->payment_status == 'paid')
                                    bg-green-100 text-green-800
                                @elseif ($order->payment_status == 'failed')
                                    bg-red-100 text-red-800
                                @else
                                    bg-yellow-100 text-yellow-800
                                @endif
                            ">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                        @if ($order->payment_intent_id)
                            <p class="text-sm text-gray-600"><span class="font-medium">ID de paiement :</span> {{ $order->payment_intent_id }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Détails des articles</h3>

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
                                        Sous-total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if ($order->orderItems && $order->orderItems->count() > 0)
                                    @foreach ($order->orderItems as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if ($item->dish && $item->dish->image)
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $item->dish->image) }}" alt="{{ $item->dish->name }}">
                                                        </div>
                                                    @endif
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $item->dish ? $item->dish->name : ($item->name ?? 'Plat supprimé') }}
                                                        </div>
                                                        @if ($item->description)
                                                            <div class="text-sm text-gray-500">
                                                                {{ $item->description }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($item->price, 2) }} €</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($item->quantity * $item->price, 2) }} €</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            Aucun article trouvé pour cette commande.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                        Sous-total
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($order->subtotal ?? 0, 2) }} €
                                    </td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                        Frais de livraison
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($order->delivery_fee ?? 0, 2) }} €
                                    </td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                        Total
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($order->total ?? 0, 2) }} €
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($order->notes)
                        <div class="mt-6">
                            <h4 class="font-medium text-gray-700 mb-2">Notes</h4>
                            <p class="text-sm text-gray-600 p-4 bg-gray-50 rounded">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
