<!-- resources/views/restaurants/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Détails du restaurant {{ $restaurant->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <p>ID: {{ $restaurant->id }}</p>
                    <p>Nom: {{ $restaurant->name }}</p>
                    <p>Créé le: {{ $restaurant->created_at }}</p>
                    <p>Modifié le: {{ $restaurant->updated_at }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
