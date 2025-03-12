<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() ||
               $user->id === $order->user_id ||
               ($user->isRestaurateur() && $user->restaurants->contains('id', $order->restaurant_id));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin() ||
               ($user->isRestaurateur() && $user->restaurants->contains('id', $order->restaurant_id));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        //
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->isAdmin() ||
               ($user->isRestaurateur() && $user->restaurants->contains('id', $order->restaurant_id));
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->isAdmin() ||
               $user->id === $order->user_id ||
               ($user->isRestaurateur() && $user->restaurants->contains('id', $order->restaurant_id));
    }
}
