<!-- resources/views/items/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Détails de l'article {{ $item->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <p>ID: {{ $item->id }}</p>
                    <p>Nom: {{ $item->name }}</p>
                    <p>Créé le: {{ $item->created_at }}</p>
                    <p>Modifié le: {{ $item->updated_at }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
