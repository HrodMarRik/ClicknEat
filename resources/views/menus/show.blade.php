@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Menu: {{ $menu->name }}</h2>
            <div>
                <a href="{{ route('restaurants.show', $restaurant) }}" class="text-indigo-600 hover:text-indigo-800 mr-4">
                    &larr; Retour au restaurant
                </a>
                @if(auth()->user()->id == $restaurant->user_id || auth()->user()->role == 'admin')
                    <a href="{{ route('restaurants.menus.edit', [$restaurant, $menu]) }}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                        Modifier le menu
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-2/3">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $menu->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $menu->description }}</p>

                        <div class="flex items-center mb-4">
                            <span class="text-gray-700 mr-2">Disponibilité:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $menu->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $menu->is_available ? 'Disponible' : 'Non disponible' }}
                            </span>
                        </div>
                    </div>

                    <div class="md:w-1/3 flex justify-end">
                        @if(auth()->user()->id == $restaurant->user_id || auth()->user()->role == 'admin')
                            <a href="{{ route('restaurants.menus.items.create', [$restaurant, $menu]) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Ajouter un plat
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Items -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($menu->items as $item)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        @if($item->image)
                            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-48 object-cover rounded-lg mb-4">
                        @else
                            <div class="w-full h-48 bg-gray-200 rounded-lg mb-4 flex items-center justify-center">
                                <span class="text-gray-500">Pas d'image</span>
                            </div>
                        @endif

                        <h4 class="text-lg font-semibold text-gray-800 mb-2">{{ $item->name }}</h4>
                        <p class="text-sm text-gray-600 mb-2">{{ $item->description }}</p>
                        <p class="text-indigo-600 font-semibold mb-2">{{ number_format($item->price, 2) }} €</p>

                        <div class="flex items-center mb-4">
                            <span class="text-xs text-gray-500 bg-gray-100 rounded-full px-2 py-1">{{ ucfirst($item->category) }}</span>
                        </div>

                        @if(auth()->user()->id == $restaurant->user_id || auth()->user()->role == 'admin')
                            <div class="flex justify-between">
                                <a href="{{ route('restaurants.menus.items.edit', [$restaurant, $menu, $item]) }}" class="text-yellow-600 hover:text-yellow-800">
                                    Modifier
                                </a>
                                <form action="{{ route('restaurants.menus.items.destroy', [$restaurant, $menu, $item]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plat?')">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Ajouter au panier
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <p class="text-gray-600 text-center">Aucun plat disponible dans ce menu.</p>
                        @if(auth()->user()->id == $restaurant->user_id || auth()->user()->role == 'admin')
                            <div class="text-center mt-4">
                                <a href="{{ route('restaurants.menus.items.create', [$restaurant, $menu]) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Ajouter un plat
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
