@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Tableau de bord administrateur</h1>

    <!-- Statistiques générales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text display-4">{{ $stats['users'] }}</p>
                    <a href="{{ route('admin.users.index') }}" class="text-white">Voir tous <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Restaurants</h5>
                    <p class="card-text display-4">{{ $stats['restaurants'] }}</p>
                    <a href="{{ route('admin.restaurants.index') }}" class="text-white">Voir tous <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Commandes</h5>
                    <p class="card-text display-4">{{ $stats['orders'] }}</p>
                    <a href="{{ route('admin.orders.index') }}" class="text-white">Voir toutes <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Revenus</h5>
                    <p class="card-text display-4">{{ number_format($stats['revenue'], 2) }} €</p>
                    <a href="{{ route('admin.orders.index') }}" class="text-white">Détails <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Commandes récentes avec options de modification -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Commandes récentes</span>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">Voir toutes</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Restaurant</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $order->user_id) }}">
                                                {{ $order->user->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.restaurants.show', $order->restaurant_id) }}">
                                                {{ $order->restaurant->name }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($order->total, 2) }} €</td>
                                        <td>
                                            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="d-inline status-form">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" class="form-select form-select-sm status-select" onchange="this.form.submit()" style="width: auto;">
                                                    @foreach($statuses as $key => $value)
                                                        <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune commande récente</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restaurants populaires -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Restaurants populaires</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($popularRestaurants as $restaurant)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.restaurants.show', $restaurant) }}">
                                    {{ $restaurant->name }}
                                </a>
                                <span class="badge bg-primary rounded-pill">{{ $restaurant->orders_count }} commandes</span>
                            </li>
                        @empty
                            <li class="list-group-item">Aucun restaurant trouvé</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Utilisateurs actifs -->
            <div class="card">
                <div class="card-header">Clients actifs</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($activeUsers as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.users.show', $user) }}">
                                    {{ $user->name }}
                                </a>
                                <span class="badge bg-primary rounded-pill">{{ $user->orders_count }} commandes</span>
                            </li>
                        @empty
                            <li class="list-group-item">Aucun utilisateur actif trouvé</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

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
@endsection
