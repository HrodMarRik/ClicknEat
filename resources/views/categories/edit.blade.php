<!-- resources/views/categories/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifier la catégorie {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="post" action="{{ route('categories.update', $category->id) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="name">Nom de la catégorie</label>
                            <input type="text" name="name" value="{{ $category->name }}">
                        </div>
                        <div>
                            <label for="restaurant_id">Restaurant</label>
                            <select name="restaurant_id">
                                @foreach($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}" {{ $category->restaurant_id == $restaurant->id ? 'selected' : '' }}>
                                        {{ $restaurant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
