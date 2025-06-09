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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Commande #{{ $order->id }}</h2>
                            <p class="text-gray-600">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'preparing') bg-blue-100 text-blue-800
                                @elseif($order->status === 'ready') bg-green-100 text-green-800
                                @elseif($order->status === 'delivered') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                @switch($order->status)
                                    @case('pending')
                                        En attente
                                        @break
                                    @case('preparing')
                                        En préparation
                                        @break
                                    @case('ready')
                                        Prêt
                                        @break
                                    @case('delivered')
                                        Livré
                                        @break
                                    @case('cancelled')
                                        Annulé
                                        @break
                                    @default
                                        {{ $order->status }}
                                @endswitch
                            </span>
                        </div>
                    </div>

                    <!-- Informations client -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Informations client</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p><strong>Nom :</strong> {{ $order->user->name }}</p>
                            <p><strong>Email :</strong> {{ $order->user->email }}</p>
                            <p><strong>Téléphone :</strong> {{ $order->user->phone }}</p>
                            <p><strong>Adresse de livraison :</strong> {{ $order->delivery_address }}</p>
                        </div>
                    </div>

                    <!-- Articles commandés -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Articles commandés</h3>
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
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
                                                @if($item->special_instructions)
                                                    <div class="text-sm text-gray-500">
                                                        Instructions : {{ $item->special_instructions }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ number_format($item->price, 2) }} €
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ number_format($item->price * $item->quantity, 2) }} €
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                            Sous-total
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($order->subtotal, 2) }} €
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                            Frais de livraison
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($order->delivery_fee, 2) }} €
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                            Total
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            {{ number_format($order->total, 2) }} €
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                            <div class="flex space-x-4">
                                @if($order->status === 'pending')
                                    <form action="{{ route('restaurateur.orders.update', $order) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="preparing">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Commencer la préparation
                                        </button>
                                    </form>
                                @elseif($order->status === 'preparing')
                                    <form action="{{ route('restaurateur.orders.update', $order) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="ready">
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Marquer comme prêt
                                        </button>
                                    </form>
                                @endif

                                @if($order->status !== 'cancelled')
                                    <form action="{{ route('restaurateur.orders.update', $order) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')"
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Annuler la commande
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
