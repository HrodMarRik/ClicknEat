<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifier l'article {{ $item->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="post" action="{{ route('items.update', $item->id) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="name">Nom</label>
                            <input type="text" name="name" value="{{ $item->name }}" required>
                        </div>
                        <div>
                            <label for="cost">Coût</label>
                            <input type="number" name="cost" value="{{ $item->cost }}">
                        </div>
                        <div>
                            <label for="price">Prix</label>
                            <input type="number" name="price" value="{{ $item->price }}" required>
                        </div>
                        <div>
                            <label for="category_id">Catégorie</label>
                            <select name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="is_active">Actif</label>
                            <input type="checkbox" name="is_active" {{ $item->is_active ? 'checked' : '' }}>
                        </div>
                        <button type="submit">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
