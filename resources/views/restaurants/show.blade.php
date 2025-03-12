@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Informations du restaurant -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3 mb-4 md:mb-0">
                    @if($restaurant->image)
                        <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" class="w-full h-auto rounded-lg">
                    @else
                        <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                            <span class="text-gray-500">Aucune image</span>
                        </div>
                    @endif
                </div>
                <div class="md:w-2/3 md:pl-6">
                    <h2 class="text-2xl font-semibold text-gray-800">{{ $restaurant->name }}</h2>

                    @if($reviewsCount > 0)
                        <div class="flex items-center mt-2">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($averageRating))
                                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-600">{{ number_format($averageRating, 1) }} ({{ $reviewsCount }} avis)</span>
                        </div>
                    @endif

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

                            @if(is_array($openingHours))
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($days as $key => $day)
                                        <div class="flex justify-between">
                                            <span class="font-medium">{{ $day }}:</span>
                                            <span>
                                                @if(isset($openingHours[$key]) && !empty($openingHours[$key]))
                                                    @foreach($openingHours[$key] as $hours)
                                                        {{ str_replace('-', ' - ', $hours) }}@if(!$loop->last), @endif
                                                    @endforeach
                                                @else
                                                    Fermé
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                {{ $restaurant->opening_hours }}
                            @endif
                        </div>
                    </div>

                    <p class="text-gray-700 mt-4">{{ $restaurant->description }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu du restaurant -->
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
                                        <div class="ml-4">
                                            @auth
                                                <form action="{{ route('cart.add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="dish_id" value="{{ $dish->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                        Ajouter
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('login') }}" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    Ajouter
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-gray-500">Aucun plat disponible pour le moment.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Avis des clients -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Avis des clients</h3>

            @if($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="font-medium text-gray-900">{{ $review->user->name }}</div>
                                    <div class="ml-4 flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</div>
                            </div>
                            <div class="mt-2 text-sm text-gray-700">{{ $review->comment }}</div>

                            @auth
                                @if(auth()->id() === $review->user_id)
                                    <div class="mt-2 flex justify-end">
                                        <form action="{{ route('reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:text-red-900">Supprimer</button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-gray-500">Aucun avis pour le moment.</p>
                </div>
            @endif

            @auth
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Laisser un avis</h4>
                    <form action="{{ route('reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

                        <div class="mb-4">
                            <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="mr-2">
                                        <input type="radio" id="rating-{{ $i }}" name="rating" value="{{ $i }}" class="hidden peer" required>
                                        <label for="rating-{{ $i }}" class="cursor-pointer peer-checked:text-yellow-500 text-gray-300">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </label>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Commentaire</label>
                            <textarea id="comment" name="comment" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Publier mon avis
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="mt-8 text-center">
                    <p class="text-gray-500">Connectez-vous pour laisser un avis.</p>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 mt-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Se connecter
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection
