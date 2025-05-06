<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Affiche le formulaire de création d'un utilisateur
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,restaurateur,user',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche les détails d'un utilisateur
     */
    public function show(User $user)
    {
        if ($user->role === 'restaurateur') {
            $user->load('restaurants');
        } elseif ($user->role === 'client') {
            $user->load(['orders' => function($query) {
                $query->orderBy('created_at', 'desc');
            }, 'orders.restaurant']);
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Met à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,restaurateur,client',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;
        $user->is_active = $request->has('is_active');

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Active un utilisateur
     */
    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur activé avec succès.');
    }

    /**
     * Désactive un utilisateur
     */
    public function deactivate(User $user)
    {
        // Empêcher la désactivation de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur désactivé avec succès.');
    }
}
