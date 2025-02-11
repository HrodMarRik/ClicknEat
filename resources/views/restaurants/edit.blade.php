<!-- resources/views/restaurants/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifier le restaurant {{ $restaurant->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="post" action="{{ route('restaurants.update', $restaurant->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="text" name="name" value="{{ $restaurant->name }}">
                        <button type="submit">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
