<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails du restaurant') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="text-indigo-600 hover:text-indigo-800">
                                {{ __('Modifier') }}
                            </a>
                            <x-delete-form
                                :route="route('admin.restaurants.destroy', $restaurant)"
                                message="Êtes-vous sûr de vouloir supprimer ce restaurant ? Cette action est irréversible."
                            />
                            <a href="{{ route('admin.restaurants.index') }}" class="text-gray-600 hover:text-gray-800">
                                &larr; {{ __('Retour') }}
                            </a>
                        </div>
                    </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>{{ $restaurant->name }}</h4>
                            <p class="text-muted">
                                <i class="bi bi-person"></i> Géré par: {{ $restaurant->user->name }}
                            </p>
                            <p>
                                <i class="bi bi-envelope"></i> {{ $restaurant->email }}
                            </p>
                            <p>
                                <i class="bi bi-telephone"></i> {{ $restaurant->phone }}
                            </p>
                            <p>
                                <i class="bi bi-geo-alt"></i> {{ $restaurant->address }}, {{ $restaurant->postal_code }} {{ $restaurant->city }}
                            </p>
                            <p>
                                <i class="bi bi-truck"></i> Frais de livraison: {{ number_format($restaurant->delivery_fee, 2) }} €
                            </p>
                            <p>
                                <i class="bi bi-cart"></i> Commande minimum: {{ number_format($restaurant->minimum_order, 2) }} €
                            </p>
                            <p>
                                <i class="bi bi-clock"></i> Temps de livraison estimé: {{ $restaurant->delivery_time }} minutes
                            </p>
                            <p>
                                <i class="bi bi-toggle-{{ $restaurant->is_active ? 'on' : 'off' }}"></i>
                                Statut: <span class="badge bg-{{ $restaurant->is_active ? 'success' : 'danger' }}">
                                    {{ $restaurant->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Description</div>
                                <div class="card-body">
                                    {{ $restaurant->description }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catégories -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Catégories ({{ $restaurant->categories->count() }})</span>
                                    <button class="btn btn-sm btn-success" disabled>
                                        <i class="bi bi-plus"></i> Ajouter
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if($restaurant->categories->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Description</th>
                                                        <th>Plats</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($restaurant->categories as $category)
                                                        <tr>
                                                            <td>{{ $category->name }}</td>
                                                            <td>{{ Str::limit($category->description, 50) }}</td>
                                                            <td>{{ $category->dishes->count() }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-primary" disabled>
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger" disabled>
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">Aucune catégorie pour ce restaurant.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plats -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Plats ({{ $restaurant->dishes->count() }})</span>
                                    <button class="btn btn-sm btn-success" disabled>
                                        <i class="bi bi-plus"></i> Ajouter
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if($restaurant->dishes->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Description</th>
                                                        <th>Prix</th>
                                                        <th>Catégorie</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($restaurant->dishes as $dish)
                                                        <tr>
                                                            <td>{{ $dish->name }}</td>
                                                            <td>{{ Str::limit($dish->description, 50) }}</td>
                                                            <td>{{ number_format($dish->price, 2) }} €</td>
                                                            <td>{{ $dish->category->name ?? 'Non catégorisé' }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $dish->is_active ? 'success' : 'danger' }}">
                                                                    {{ $dish->is_active ? 'Actif' : 'Inactif' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-primary" disabled>
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger" disabled>
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">Aucun plat pour ce restaurant.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
