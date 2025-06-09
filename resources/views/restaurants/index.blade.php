<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nos restaurants') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('restaurants.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                        <div>
                            <select name="cuisine" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Toutes les cuisines</option>
                                @foreach($cuisines as $cuisine)
                                    <option value="{{ $cuisine }}" {{ request('cuisine') == $cuisine ? 'selected' : '' }}>{{ ucfirst($cuisine) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-grow">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un restaurant..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <button type="submit" class="btn-primary">
                                Rechercher
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($restaurants->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($restaurants as $restaurant)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <a href="{{ route('restaurants.show', $restaurant) }}" class="block">
                                <div class="h-48 bg-gray-200 relative">
                                    @if($restaurant->image)
                                        <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-gray-500">Aucune image</span>
                                        </div>
                                    @endif
                                    @if($restaurant->cuisine_type)
                                        <span class="absolute top-2 right-2 bg-white px-2 py-1 rounded text-xs font-medium">{{ ucfirst($restaurant->cuisine_type) }}</span>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $restaurant->name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($restaurant->description, 100) }}</p>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $restaurant->city }}
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $restaurant->delivery_time ?? '30-45' }} min
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Minimum: {{ number_format($restaurant->minimum_order ?? 10, 2) }} €
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11a4 4 0 11-8 0 4 4 0 018 0zm-4-8a8 8 0 00-8 8c0 1.892.402 3.13 1.5 4.5l6.5 6.5 6.5-6.5c1.098-1.37 1.5-2.608 1.5-4.5a8 8 0 00-8-8z"></path>
                                        </svg>
                                        Livraison: {{ number_format($restaurant->delivery_fee ?? 2.99, 2) }} €
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $restaurants->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <p class="text-gray-500 text-center">Aucun restaurant trouvé.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
