@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Finaliser la commande</h2>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Adresse de livraison</h3>

                        <form action="{{ route('checkout.process') }}" method="POST" id="payment-form">
                            @csrf

                            <div class="mb-4">
                                <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Adresse</label>
                                <input type="text" name="address" id="address" value="{{ old('address', auth()->user()->address) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('address')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="city" class="block text-gray-700 text-sm font-bold mb-2">Ville</label>
                                <input type="text" name="city" id="city" value="{{ old('city', auth()->user()->city) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('city')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="postal_code" class="block text-gray-700 text-sm font-bold mb-2">Code postal</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', auth()->user()->postal_code) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('postal_code')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Téléphone</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('phone')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Instructions de livraison (optionnel)</label>
                                <textarea name="notes" id="notes" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <h3 class="text-lg font-medium text-gray-900 mb-4 mt-8">Paiement</h3>

                            <div class="mb-4">
                                <div id="card-element" class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <!-- Stripe Card Element -->
                                </div>
                                <div id="card-errors" role="alert" class="text-red-500 text-xs italic mt-1"></div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" id="submit-button" class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Payer et commander
                                </button>
                            </div>
                        </form>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Récapitulatif de la commande</h3>

                        <div class="border rounded-lg overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <h4 class="font-medium text-gray-900">{{ $cart->restaurant->name }}</h4>
                            </div>

                            <div class="p-4 space-y-3">
                                @foreach($cart->items as $item)
                                    <div class="flex justify-between">
                                        <div class="flex items-center">
                                            <span class="text-gray-700">{{ $item->quantity }}x</span>
                                            <span class="ml-2 text-gray-900">{{ $item->dish->name }}</span>
                                        </div>
                                        <span class="text-gray-900">{{ number_format($item->dish->price * $item->quantity, 2) }} €</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="px-4 py-3 bg-gray-50 border-t">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">Sous-total</span>
                                    <span class="text-gray-900">{{ number_format($cart->subtotal, 2) }} €</span>
                                </div>
                                <div class="flex justify-between text-sm mt-2">
                                    <span class="text-gray-700">Frais de livraison</span>
                                    <span class="text-gray-900">{{ number_format($cart->delivery_fee, 2) }} €</span>
                                </div>
                                <div class="flex justify-between font-medium text-base mt-4">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-gray-900">{{ number_format($cart->total, 2) }} €</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('cart.index') }}" class="text-indigo-600 hover:text-indigo-800">
                                &larr; Retour au panier
                            </a>
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
    document.addEventListener('DOMContentLoaded', function() {
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        const elements = stripe.elements();

        const style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        const cardElement = elements.create('card', {style: style});
        cardElement.mount('#card-element');

        const cardErrors = document.getElementById('card-errors');
        const submitButton = document.getElementById('submit-button');
        const form = document.getElementById('payment-form');

        cardElement.addEventListener('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
            } else {
                cardErrors.textContent = '';
            }
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            submitButton.disabled = true;

            stripe.createToken(cardElement).then(function(result) {
                if (result.error) {
                    cardErrors.textContent = result.error.message;
                    submitButton.disabled = false;
                } else {
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            form.submit();
        }
    });
</script>
@endpush
@endsection
