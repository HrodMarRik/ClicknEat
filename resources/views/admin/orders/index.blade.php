@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Gestion des commandes') }} ({{ $orders->total() }} commandes trouvées)</span>
                </div>

                <div class="card-body">
                    <!-- Débogage -->
                    <div class="alert alert-info">
                        Nombre de commandes récupérées : {{ $orders->count() }} sur {{ $orders->total() }}
                    </div>

                    <!-- Filtres -->
                    <div class="mb-4">
                        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
                            <div class="col-md-2">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="user_id" class="form-control">
                                    <option value="">Tous les clients</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="restaurant_id" class="form-control">
                                    <option value="">Tous les restaurants</option>
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}" {{ request('restaurant_id') == $restaurant->id ? 'selected' : '' }}>{{ $restaurant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">Tous les statuts</option>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-text">Du</span>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-text">Au</span>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-primary">Filtrer</button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Réinitialiser</a>
                            </div>
                        </form>
                    </div>

                    <!-- Liste des commandes -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Restaurant</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
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
                                            <span class="badge bg-{{
                                                $order->status === 'delivered' ? 'success' :
                                                ($order->status === 'cancelled' ? 'danger' :
                                                ($order->status === 'pending' ? 'warning' : 'info'))
                                            }}">
                                                {{ $statuses[$order->status] ?? $order->status }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <x-delete-form
                                                :route="route('admin.orders.destroy', $order)"
                                                message="Êtes-vous sûr de vouloir supprimer cette commande ? Cette action est irréversible."
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune commande trouvée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
