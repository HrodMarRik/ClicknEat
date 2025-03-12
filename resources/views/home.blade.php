<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accueil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenu de la page d'accueil -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold mb-4">Bienvenue sur notre plateforme de livraison de repas</h1>

                    <!-- Restaurants les mieux notés -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Restaurants les mieux notés</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($topRatedRestaurants as $restaurant)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                    @if ($restaurant->image)
                                        <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">Pas d'image</span>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold">{{ $restaurant->name }}</h3>
                                        <div class="flex items-center mt-1">
                                            <span class="text-yellow-500">★</span>
                                            <span class="ml-1 text-sm text-gray-600">{{ number_format($restaurant->reviews_avg_rating ?? 0, 1) }}/5</span>
                                        </div>
                                        <p class="text-gray-600 text-sm mt-2">{{ Str::limit($restaurant->description, 100) }}</p>
                                        <a href="{{ route('restaurants.show', $restaurant) }}" class="mt-3 inline-block text-indigo-600 hover:text-indigo-800">Voir le menu</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <!-- Nouveaux restaurants -->
                    <section>
                        <h2 class="text-xl font-semibold mb-4">Nouveaux restaurants</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($newRestaurants as $restaurant)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                    @if ($restaurant->image)
                                        <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">Pas d'image</span>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold">{{ $restaurant->name }}</h3>
                                        <div class="flex items-center mt-1">
                                            <span class="text-yellow-500">★</span>
                                            <span class="ml-1 text-sm text-gray-600">{{ number_format($restaurant->averageRating ?? 0, 1) }}/5</span>
                                        </div>
                                        <p class="text-gray-600 text-sm mt-2">{{ Str::limit($restaurant->description, 100) }}</p>
                                        <a href="{{ route('restaurants.show', $restaurant) }}" class="mt-3 inline-block text-indigo-600 hover:text-indigo-800">Voir le menu</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
