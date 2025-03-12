@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Commander chez {{ $restaurant->name }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Menu Items -->
            <div class="md:col-span-2">
                @forelse($restaurant->menus as $menu)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">{{ $menu->name }}</h3>

                            @foreach($menu->items->groupBy('category') as $category => $items)
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-700 mb-3">{{ $category }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($items as $item)
                                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                                <div class="flex">
                                                    @if($item->image)
                                                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-24 h-24 object-cover rounded-lg mr-4">
                                                    @else
                                                        <div class="w-24 h-24 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                                            <span class="text-gray-500 text-xs">Pas d'image</span>
                                                        </div>
                                                    @endif
                                                    <div class="flex-1">
                                                        <h5 class="font-semibold text-gray-800">{{ $item->name }}</h5>
                                                        <p class="text-sm text-gray-600 mb-2">{{ $item->description }}</p>
                                                        <div class="flex justify-between items-center">
                                                            <span class="font-bold text-indigo-600">{{ number_format($item->price, 2) }} €</span>
                                                            @if($item->available)
                                                                <form action="{{ route('cart.add', $item) }}" method="POST">
                                                                    @csrf
                                                                    <div class="flex items-center">
                                                                        <input type="number" name="quantity" value="1" min="1" max="10" class="w-16 mr-2 shadow appearance-none border rounded py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                                            Ajouter
                                                                        </button>
                                                                    </div>
                                                                    <textarea name="special_instructions" placeholder="Instructions spéciales" class="mt-2 w-full text-xs shadow appearance-none border rounded py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                                                                </form>
                                                            @else
                                                                <span class="text-red-500 text-sm">Non disponible</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <p class="text-gray-600 text-center">Aucun menu disponible pour ce restaurant.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Cart Summary -->
            <div class="md:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Votre panier</h3>

                        @if(count($cart) > 0)
                            <div class="mb-4">
                                @foreach($cart as $id => $details)
                                    <div class="flex justify-between items-start mb-3 pb-3 border-b">
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $details['name'] }}</h4>
                                            <p class="text-sm text-gray-600">{{ $details['quantity'] }} x {{ number_format($details['price'], 2) }} €</p>
                                            @if(!empty($details['special_instructions']))
                                                <p class="text-xs text-gray-500 italic">{{ $details['special_instructions'] }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center">
                                            <span class="font-bold text-indigo-600 mr-2">{{ number_format($details['price'] * $details['quantity'], 2) }} €</span>
                                            <form action="{{ route('cart.remove') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $id }}">
                                                <button type="submit" class="text-red-500 hover:text-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-4 pt-2 border-t">
                                <div class="flex justify-between items-center font-bold text-lg">
                                    <span>Total</span>
                                    <span class="text-indigo-600">
                                        {{ number_format(array_reduce($cart, function($carry, $item) {
                                            return $carry + ($item['price'] * $item['quantity']);
                                        }, 0), 2) }} €
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('cart') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-center">
                                    Voir le panier
                                </a>
                                <form action="{{ route('cart.clear') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                                        Vider le panier
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="text-gray-600 text-center">Votre panier est vide.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection