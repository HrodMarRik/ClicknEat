<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Items;
use Illuminate\Auth\Access\Response;

class ItemsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view items');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Items $items): bool
    {
        return $user->hasPermissionTo('view items');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create items');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Items $items): bool
    {
        return $user->hasPermissionTo('update items');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Items $items): bool
    {
        return $user->hasPermissionTo('delete items');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Items $items): bool
    {
        return $user->hasPermissionTo('restore items');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Items $items): bool
    {
        return $user->hasPermissionTo('force delete items');
    }
}
