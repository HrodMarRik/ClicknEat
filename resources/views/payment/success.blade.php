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
                    <h2 class="text-2xl font-semibold text-gray-800 mt-4">Paiement réussi!</h2>
                    <p class="text-gray-600 mt-2">Votre paiement pour la commande #{{ $order->id }} a été traité avec succès.</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Détails de la commande</h3>
                    <p class="text-gray-600">Restaurant: {{ $order->restaurant->name }}</p>
                    <p class="text-gray-600">Date: {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                    <p class="text-gray-600">Statut:
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            Confirmée
                        </span>
                    </p>
                    <p class="text-gray-600">Paiement:
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            Payé
                        </span>
                    </p>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Montant payé</h3>
                    <p class="font-bold text-indigo-600 text-lg">{{ number_format($order->total_price, 2) }} €</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Informations de livraison</h3>
                    <p class="text-gray-600">Adresse: {{ $order->delivery_address }}</p>
                    <p class="text-gray-600">Téléphone: {{ $order->phone }}</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Prochaines étapes</h3>
                    <p class="text-gray-600">Votre commande a été confirmée et est en cours de préparation. Vous recevrez une notification lorsqu'elle sera prête pour la livraison.</p>
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('home') }}" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                        Retour à l'accueil
                    </a>
                    <a href="{{ route('orders.show', $order) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Voir les détails de la commande
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection