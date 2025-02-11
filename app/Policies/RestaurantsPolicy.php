<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Restaurants;
use Illuminate\Auth\Access\Response;

class RestaurantsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view restaurants');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Restaurants $restaurants): bool
    {
        return $user->hasPermissionTo('view restaurants');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create restaurants');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Restaurants $restaurants): bool
    {
        return $user->hasPermissionTo('update restaurants');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Restaurants $restaurants): bool
    {
        return $user->hasPermissionTo('delete restaurants');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Restaurants $restaurants): bool
    {
        return $user->hasPermissionTo('restore restaurants');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Restaurants $restaurants): bool
    {
        return $user->hasPermissionTo('force delete restaurants');
    }
}
