<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord administrateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques générales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-indigo-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <h3 class="text-lg font-semibold mb-2">Utilisateurs</h3>
                        <p class="text-3xl font-bold mb-2">{{ $stats['users'] }}</p>
                        <a href="{{ route('admin.users.index') }}" class="text-white hover:text-indigo-200">
                            Voir tous →
                        </a>
                    </div>
                </div>
                <div class="bg-green-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <h3 class="text-lg font-semibold mb-2">Restaurants</h3>
                        <p class="text-3xl font-bold mb-2">{{ $stats['restaurants'] }}</p>
                        <a href="{{ route('admin.restaurants.index') }}" class="text-white hover:text-green-200">
                            Voir tous →
                        </a>
                    </div>
                </div>
                <div class="bg-blue-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <h3 class="text-lg font-semibold mb-2">Commandes</h3>
                        <p class="text-3xl font-bold mb-2">{{ $stats['orders'] }}</p>
                        <a href="{{ route('admin.orders.index') }}" class="text-white hover:text-blue-200">
                            Voir toutes →
                        </a>
                    </div>
                </div>
                <div class="bg-yellow-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <h3 class="text-lg font-semibold mb-2">Revenus</h3>
                        <p class="text-3xl font-bold mb-2">{{ number_format($stats['revenue'], 2) }} €</p>
                        <a href="{{ route('admin.orders.index') }}" class="text-white hover:text-yellow-200">
                            Détails →
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Commandes récentes -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Commandes récentes</h3>
                                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Voir toutes
                                </a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($recentOrders as $order)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->id }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <a href="{{ route('admin.users.show', $order->user_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $order->user->name }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <a href="{{ route('admin.restaurants.show', $order->restaurant_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $order->restaurant->name }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($order->total, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="inline status-form">
                                                        @csrf
                                                        @method('PUT')
                                                        <select name="status" class="status-select rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                                            @foreach($statuses as $key => $value)
                                                                <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                        Voir
                                                    </a>
                                                    <a href="{{ route('admin.orders.edit', $order) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Modifier
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center">Aucune commande récente</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne de droite -->
                <div class="space-y-4">
                    <!-- Restaurants populaires -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">Restaurants populaires</h3>
                            <div class="space-y-2">
                                @forelse($popularRestaurants as $restaurant)
                                    <div class="flex justify-between items-center">
                                        <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $restaurant->name }}
                                        </a>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $restaurant->orders_count }} commandes
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">Aucun restaurant trouvé</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Utilisateurs actifs -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">Clients actifs</h3>
                            <div class="space-y-2">
                                @forelse($activeUsers as $user)
                                    <div class="flex justify-between items-center">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $user->name }}
                                        </a>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $user->orders_count }} commandes
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">Aucun utilisateur actif trouvé</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Script pour soumettre automatiquement le formulaire lors du changement de statut
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelects = document.querySelectorAll('.status-select');
            statusSelects.forEach(select => {
                select.addEventListener('change', function() {
                    this.closest('form').submit();
                });
            });
        });
    </script>
    @endpush
</x-admin-layout>
