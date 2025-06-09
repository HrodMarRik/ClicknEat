<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des restaurants') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
                    &larr; Retour au tableau de bord
                </a>
            </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <form action="{{ route('admin.restaurants.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div>
                    <select name="cuisine" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Toutes les cuisines</option>
                        <option value="française" {{ request('cuisine') == 'française' ? 'selected' : '' }}>Française</option>
                        <option value="italienne" {{ request('cuisine') == 'italienne' ? 'selected' : '' }}>Italienne</option>
                        <option value="japonaise" {{ request('cuisine') == 'japonaise' ? 'selected' : '' }}>Japonaise</option>
                        <option value="chinoise" {{ request('cuisine') == 'chinoise' ? 'selected' : '' }}>Chinoise</option>
                        <option value="indienne" {{ request('cuisine') == 'indienne' ? 'selected' : '' }}>Indienne</option>
                        <option value="mexicaine" {{ request('cuisine') == 'mexicaine' ? 'selected' : '' }}>Mexicaine</option>
                        <option value="libanaise" {{ request('cuisine') == 'libanaise' ? 'selected' : '' }}>Libanaise</option>
                        <option value="américaine" {{ request('cuisine') == 'américaine' ? 'selected' : '' }}>Américaine</option>
                    </select>
                </div>
                <div class="flex-grow">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un restaurant..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Tous les statuts</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Filtrer
                    </button>
                    <a href="{{ route('admin.restaurants.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Restaurants List -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between mb-4">
                <h3 class="text-lg font-semibold">Liste des restaurants</h3>
                <a href="{{ route('admin.restaurants.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Ajouter un restaurant
                </a>
            </div>

            @if($restaurants->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nom
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Propriétaire
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cuisine
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($restaurants as $restaurant)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $restaurant->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($restaurant->image)
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}">
                                                </div>
                                            @else
                                                <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <span class="text-gray-500 text-xs">No img</span>
                                                </div>
                                            @endif
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $restaurant->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $restaurant->address }}, {{ $restaurant->city }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $restaurant->user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $restaurant->cuisine_type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $restaurant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $restaurant->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                            <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="text-yellow-600 hover:text-yellow-900">Éditer</a>

                                            @if($restaurant->is_active)
                                                <form method="POST" action="{{ route('admin.restaurants.deactivate', $restaurant) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button"
                                                            class="text-orange-600 hover:text-orange-900"
                                                            onclick="confirmAction('Êtes-vous sûr de vouloir désactiver ce restaurant ?', () => this.closest('form').submit())">
                                                        Désactiver
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.restaurants.activate', $restaurant) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button"
                                                            class="text-green-600 hover:text-green-900"
                                                            onclick="confirmAction('Êtes-vous sûr de vouloir activer ce restaurant ?', () => this.closest('form').submit())">
                                                        Activer
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.restaurants.destroy', $restaurant) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce restaurant ? Cette action est irréversible.')">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $restaurants->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-gray-500">Aucun restaurant trouvé.</p>
                </div>
            @endif
        </div>
            </div>
        </div>
    </div>
</x-admin-layout>

@push('scripts')
<script>
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}
</script>
@endpush
