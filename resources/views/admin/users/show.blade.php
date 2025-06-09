<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de l\'utilisateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800">
                                {{ __('Modifier') }}
                            </a>
                            <x-delete-form
                                :route="route('admin.users.destroy', $user)"
                                message="Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible."
                            />
                            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
                                &larr; {{ __('Retour') }}
                            </a>
                        </div>
                    </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4>{{ $user->name }}</h4>
                            <p class="text-muted">
                                <i class="bi bi-person"></i> Rôle:
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'restaurateur' ? 'primary' : 'success') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>
                            <p>
                                <i class="bi bi-envelope"></i> {{ $user->email }}
                            </p>
                            @if($user->phone)
                            <p>
                                <i class="bi bi-telephone"></i> {{ $user->phone }}
                            </p>
                            @endif
                            @if($user->address)
                            <p>
                                <i class="bi bi-geo-alt"></i> {{ $user->address }}
                                @if($user->postal_code || $user->city)
                                , {{ $user->postal_code }} {{ $user->city }}
                                @endif
                            </p>
                            @endif
                            <p>
                                <i class="bi bi-calendar"></i> Inscrit le: {{ $user->created_at->format('d/m/Y') }}
                            </p>
                            <p>
                                <i class="bi bi-toggle-{{ $user->is_active ? 'on' : 'off' }}"></i>
                                Statut: <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($user->role === 'restaurateur')
                    <!-- Restaurants -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <span>Restaurants</span>
                                </div>
                                <div class="card-body">
                                    @if($user->restaurants && $user->restaurants->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Adresse</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->restaurants as $restaurant)
                                                        <tr>
                                                            <td>{{ $restaurant->name }}</td>
                                                            <td>{{ $restaurant->address }}, {{ $restaurant->postal_code }} {{ $restaurant->city }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $restaurant->is_active ? 'success' : 'danger' }}">
                                                                    {{ $restaurant->is_active ? 'Actif' : 'Inactif' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="btn btn-sm btn-info">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">Aucun restaurant pour cet utilisateur.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($user->role === 'client')
                    <!-- Commandes -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <span>Commandes récentes</span>
                                </div>
                                <div class="card-body">
                                    @if($user->orders && $user->orders->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Restaurant</th>
                                                        <th>Date</th>
                                                        <th>Total</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->orders->take(5) as $order)
                                                        <tr>
                                                            <td>{{ $order->id }}</td>
                                                            <td>{{ $order->restaurant->name }}</td>
                                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                            <td>{{ number_format($order->total, 2) }} €</td>
                                                            <td>
                                                                <span class="badge bg-{{
                                                                    $order->status === 'delivered' ? 'success' :
                                                                    ($order->status === 'cancelled' ? 'danger' : 'warning')
                                                                }}">
                                                                    {{ ucfirst($order->status) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @if($user->orders->count() > 5)
                                                <div class="text-center mt-3">
                                                    <a href="{{ route('admin.orders.index', ['user_id' => $user->id]) }}" class="btn btn-sm btn-primary">
                                                        Voir toutes les commandes
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-center">Aucune commande pour cet utilisateur.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
