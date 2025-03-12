@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="text-2xl font-semibold text-gray-800">Détails de la commande #{{ $order->id ?? 'N/A' }}</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 hover:text-indigo-800">
                &larr; Retour aux commandes
            </a>
        </div>
    </div>

    @if(!$order)
        <div class="alert alert-danger" role="alert">
            <p>La commande demandée n'existe pas.</p>
        </div>
    @else
        <div class="card mb-6">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Informations de la commande</h3>
                        <p><span class="font-medium">ID:</span> {{ $order->id }}</p>
                        <p><span class="font-medium">Date:</span> {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        <p><span class="font-medium">Statut:</span>
                            <span class="badge
                                @if($order->status == 'delivered') badge-success
                                @elseif($order->status == 'pending') badge-warning
                                @elseif($order->status == 'preparing') badge-info
                                @elseif($order->status == 'delivering') badge-purple
                                @elseif($order->status == 'cancelled') badge-danger
                                @else badge-secondary @endif">
                                {{ ucfirst($order->status ?? 'inconnu') }}
                            </span>
                        </p>
                        <p><span class="font-medium">Total:</span> {{ number_format($order->total ?? 0, 2) }} €</p>
                        <p><span class="font-medium">Méthode de paiement:</span> {{ $order->payment_method ?? 'N/A' }}</p>
                        <p><span class="font-medium">Adresse de livraison:</span> {{ $order->delivery_address ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-3">Informations du client</h3>
                        @if($order->user)
                            <p><span class="font-medium">Nom:</span> {{ $order->user->name ?? 'N/A' }}</p>
                            <p><span class="font-medium">Email:</span> {{ $order->user->email ?? 'N/A' }}</p>
                            <p><span class="font-medium">Téléphone:</span> {{ $order->user->phone ?? 'Non renseigné' }}</p>
                        @else
                            <p>Informations client non disponibles</p>
                        @endif

                        <h3 class="text-lg font-semibold mt-6 mb-3">Informations du restaurant</h3>
                        @if($order->restaurant)
                            <p><span class="font-medium">Nom:</span> {{ $order->restaurant->name ?? 'N/A' }}</p>
                            <p><span class="font-medium">Adresse:</span> {{ $order->restaurant->address ?? 'N/A' }}, {{ $order->restaurant->postal_code ?? 'N/A' }} {{ $order->restaurant->city ?? 'N/A' }}</p>
                            <p><span class="font-medium">Téléphone:</span> {{ $order->restaurant->phone ?? 'N/A' }}</p>
                        @else
                            <p>Informations restaurant non disponibles</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Articles commandés</h3>

                @if($order->items && $order->items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Plat</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="text-gray-900">{{ $item->dish->name ?? 'Plat inconnu' }}</td>
                                        <td>{{ number_format($item->price ?? 0, 2) }} €</td>
                                        <td>{{ $item->quantity ?? 0 }}</td>
                                        <td>{{ number_format(($item->price ?? 0) * ($item->quantity ?? 0), 2) }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="py-4 px-6 text-sm font-medium text-gray-900 text-right">Sous-total:</td>
                                    <td class="py-4 px-6 text-sm text-gray-900">{{ number_format($order->subtotal ?? 0, 2) }} €</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="py-4 px-6 text-sm font-medium text-gray-900 text-right">Frais de livraison:</td>
                                    <td class="py-4 px-6 text-sm text-gray-900">{{ number_format($order->delivery_fee ?? 0, 2) }} €</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="py-4 px-6 text-sm font-medium text-gray-900 text-right">Total:</td>
                                    <td class="py-4 px-6 text-sm font-bold text-gray-900">{{ number_format($order->total ?? 0, 2) }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">Aucun article dans cette commande.</p>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
