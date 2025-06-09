<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $restaurant->name }}
            </h2>
            <a href="{{ route('restaurants.index') }}" class="text-indigo-600 hover:text-indigo-800">
                &larr; Retour aux restaurants
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informations du restaurant -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="relative h-64">
                    @if($restaurant->image)
                        <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500">Aucune image</span>
                        </div>
                    @endif
                    @if($restaurant->cuisine_type)
                        <span class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-medium">{{ ucfirst($restaurant->cuisine_type) }}</span>
                    @endif
                </div>
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $restaurant->name }}</h1>
                            <p class="mt-2 text-gray-600">{{ $restaurant->description }}</p>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center">
                                <span class="text-yellow-500">★</span>
                                <span class="ml-1 text-sm text-gray-600">{{ number_format($averageRating, 1) }}/5</span>
                                <span class="ml-1 text-sm text-gray-500">({{ $reviewsCount }} avis)</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Adresse</div>
                            <div class="text-sm text-gray-700">{{ $restaurant->address }}, {{ $restaurant->postal_code }} {{ $restaurant->city }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">Téléphone</div>
                            <div class="text-sm text-gray-700">{{ $restaurant->phone }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-sm text-gray-500">Horaires d'ouverture</div>
                        <div class="text-sm text-gray-700">
                            @php
                                $days = [
                                    'monday' => 'Lundi',
                                    'tuesday' => 'Mardi',
                                    'wednesday' => 'Mercredi',
                                    'thursday' => 'Jeudi',
                                    'friday' => 'Vendredi',
                                    'saturday' => 'Samedi',
                                    'sunday' => 'Dimanche'
                                ];
                                $openingHours = json_decode($restaurant->opening_hours, true);
                            @endphp
                            @foreach($days as $key => $day)
                                <div class="flex justify-between">
                                    <span>{{ $day }}</span>
                                    <span>
                                        @if(isset($openingHours[$key]))
                                            {{ $openingHours[$key]['open'] }} - {{ $openingHours[$key]['close'] }}
                                        @else
                                            Fermé
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menu -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Menu</h3>

                    @if($categories->count() > 0)
                        <div class="space-y-8">
                            @foreach($categories as $category)
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-4">{{ $category->name }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($category->dishes as $dish)
                                            <div class="flex">
                                                <div class="flex-grow">
                                                    <h5 class="text-base font-medium text-gray-900">{{ $dish->name }}</h5>
                                                    <p class="text-sm text-gray-500">{{ $dish->description }}</p>
                                                    <div class="mt-1 text-sm font-medium text-gray-900">{{ number_format($dish->price, 2) }} €</div>
                                                </div>
                                                @if($dish->image)
                                                    <div class="flex-shrink-0 ml-4">
                                                        <img src="{{ asset('storage/' . $dish->image) }}" alt="{{ $dish->name }}" class="w-20 h-20 object-cover rounded">
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center">Aucun plat disponible pour le moment.</p>
                    @endif
                </div>
            </div>

            <!-- Avis -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Avis des clients</h3>

                    @if($reviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($reviews as $review)
                                <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="flex items-center">
                                                <span class="font-medium text-gray-900">{{ $review->user->name }}</span>
                                                <span class="mx-2 text-gray-500">•</span>
                                                <span class="text-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="flex items-center mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-gray-600">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $reviews->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center">Aucun avis pour le moment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
