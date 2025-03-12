@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Paiement de la commande #{{ $order->id }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Payment Form -->
            <div class="md:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Informations de paiement</h3>

                        <form id="payment-form" action="{{ route('orders.process', $order) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="card-holder-name" class="block text-gray-700 text-sm font-bold mb-2">Nom du titulaire de la carte</label>
                                <input id="card-holder-name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            </div>

                            <div class="mb-4">
                                <label for="card-element" class="block text-gray-700 text-sm font-bold mb-2">Carte de crédit</label>
                                <div id="card-element" class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></div>
                                <div id="card-errors" class="text-red-500 text-xs italic mt-1" role="alert"></div>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-600">
                                    Pour tester, utilisez le numéro de carte 4242 4242 4242 4242, une date d'expiration future, et n'importe quel code CVC à 3 chiffres.
                                </p>
                            </div>

                            <div class="flex items-center justify-between">
                                <a href="{{ route('orders.show', $order) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Annuler
                                </a>
                                <button id="card-button" type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-secret="{{ $intent->client_secret }}">
                                    Payer {{ number_format($order->total_price, 2) }} €
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="md:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé de la commande</h3>

                        <div class="mb-4">
                            <p class="text-gray-600 mb-2">Restaurant: {{ $order->restaurant->name }}</p>
                            <p class="text-gray-600">Date: {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                        </div>

                        <div class="mb-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Articles</h4>
                            @foreach($order->items as $item)
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="text-sm text-gray-800">{{ $item->name }} x {{ $item->pivot->quantity }}</p>
                                    </div>
                                    <p class="text-sm font-medium text-gray-800">{{ number_format($item->pivot->price * $item->pivot->quantity, 2) }} €</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-4 pt-2 border-t">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-600">Sous-total</span>
                                <span class="font-medium">{{ number_format($order->total_price - 2.99, 2) }} €</span>
                            </div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-600">Frais de livraison</span>
                                <span class="font-medium">{{ number_format(2.99, 2) }} €</span>
                            </div>
                            <div class="flex justify-between items-center font-bold text-lg pt-2 border-t mt-2">
                                <span>Total</span>
                                <span class="text-indigo-600">{{ number_format($order->total_price, 2) }} €</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config('cashier.key') }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card');

    cardElement.mount('#card-element');

    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;
    const form = document.getElementById('payment-form');

    cardElement.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        cardButton.disabled = true;
        cardButton.textContent = 'Traitement en cours...';

        const { setupIntent, error } = await stripe.confirmCardSetup(
            clientSecret, {
                payment_method: {
                    card: cardElement,
                    billing_details: { name: cardHolderName.value }
                }
            }
        );

        if (error) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            cardButton.disabled = false;
            cardButton.textContent = 'Payer {{ number_format($order->total_price, 2) }} €';
        } else {
            const paymentMethodInput = document.createElement('input');
            paymentMethodInput.setAttribute('type', 'hidden');
            paymentMethodInput.setAttribute('name', 'payment_method');
            paymentMethodInput.setAttribute('value', setupIntent.payment_method);
            form.appendChild(paymentMethodInput);

            form.submit();
        }
    });
</script>
@endpush
@endsection