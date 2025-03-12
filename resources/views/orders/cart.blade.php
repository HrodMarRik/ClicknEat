@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Votre panier</h2>

        @if(count($cart) > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Cart Items -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Articles ({{ count($cart) }})</h3>

                            <div class="mb-4">
                                @foreach($cart as $id => $details)
                                    <div class="flex justify-between items-start mb-4 pb-4 border-b">
                                        <div class="flex">
                                            <div class="mr-4">
                                                <form action="{{ route('cart.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="item_id" value="{{ $id }}">
                                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-800">{{ $details['name'] }}</h4>
                                                <p class="text-sm text-gray-600">{{ number_format($details['price'], 2) }} €</p>
                                                @if(!empty($details['special_instructions']))
                                                    <p class="text-xs text-gray-500 italic mt-1">Instructions: {{ $details['special_instructions'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <form action="{{ route('cart.update') }}" method="POST" class="flex items-center">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $id }}">
                                                <input type="number" name="quantity" value="{{ $details['quantity'] }}" min="1" max="10" class="w-16 mr-2 shadow appearance-none border rounded py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-3 rounded text-sm">
                                                    Mettre à jour
                                                </button>
                                            </form>
                                            <span class="font-bold text-indigo-600 ml-4">{{ number_format($details['price'] * $details['quantity'], 2) }} €</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-between">
                                <a href="{{ route('restaurants.show', $restaurant) }}" class="text-indigo-600 hover:text-indigo-800">
                                    &larr; Continuer les achats
                                </a>
                                <form action="{{ route('cart.clear') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        Vider le panier
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé de la commande</h3>

                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Sous-total</span>
                                    <span class="font-medium">
                                        {{ number_format(array_reduce($cart, function($carry, $item) {
                                            return $carry + ($item['price'] * $item['quantity']);
                                        }, 0), 2) }} €
                                    </span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Frais de livraison</span>
                                    <span class="font-medium">{{ number_format(2.99, 2) }} €</span>
                                </div>
                            </div>

                            <div class="mb-4 pt-2 border-t">
                                <div class="flex justify-between items-center font-bold text-lg">
                                    <span>Total</span>
                                    <span class="text-indigo-600">
                                        {{ number_format(array_reduce($cart, function($carry, $item) {
                                            return $carry + ($item['price'] * $item['quantity']);
                                        }, 0) + 2.99, 2) }} €
                                    </span>
                                </div>
                            </div>

                            <form action="{{ route('orders.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

                                <div class="mb-4">
                                    <label for="delivery_address" class="block text-gray-700 text-sm font-bold mb-2">Adresse de livraison</label>
                                    <textarea name="delivery_address" id="delivery_address" rows="3" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('delivery_address', auth()->user()->address) }}</textarea>
                                    @error('delivery_address')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Numéro de téléphone</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('phone')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes supplémentaires</label>
                                    <textarea name="notes" id="notes" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes') }}</textarea>
                                </div>

                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Passer la commande
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <p class="text-gray-600 text-center mb-4">Votre panier est vide.</p>
                    <div class="text-center">
                        <a href="{{ route('restaurants.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Parcourir les restaurants
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection