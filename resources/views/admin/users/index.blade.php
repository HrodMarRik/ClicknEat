@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Gestion des utilisateurs</h2>
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
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div>
                    <select name="role" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Tous les rôles</option>
                        <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>Client</option>
                        <option value="restaurateur" {{ request('role') == 'restaurateur' ? 'selected' : '' }}>Restaurateur</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                    </select>
                </div>
                <div class="flex-grow">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou email..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <select name="sort" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récents</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Plus anciens</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nom (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nom (Z-A)</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'inscription</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr>
                                <td class="py-4 px-6 text-sm text-gray-500">{{ $user->id }}</td>
                                <td class="py-4 px-6 text-sm text-gray-900">{{ $user->name }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">{{ $user->email }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($user->role == 'admin') bg-purple-100 text-purple-800
                                        @elseif($user->role == 'restaurateur') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($user->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900">Éditer</a>

                                        @if($user->id != auth()->id()) <!-- Ne pas permettre de désactiver son propre compte -->
                                            @if($user->is_active)
                                                <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                            onclick="confirmAction('Êtes-vous sûr de vouloir désactiver cet utilisateur ?', () => this.closest('form').submit())">
                                                        <i class="bi bi-x-circle"></i> Désactiver
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success"
                                                            onclick="confirmAction('Êtes-vous sûr de vouloir activer cet utilisateur ?', () => this.closest('form').submit())">
                                                        <i class="bi bi-check-circle"></i> Activer
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">Supprimer</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
