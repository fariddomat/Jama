<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('superadministrator') || $user->hasRole('delivery_agent') || $user->hasRole('merchant');
    }

    public function view(User $user, Order $order)
    {
        if ($user->hasRole('superadministrator')) {
            return true;
        }
        if ($user->hasRole('delivery_agent')) {
            return $order->delivery_agent_id === $user->id;
        }
        if ($user->hasRole('merchant')) {
            return $order->merchant_id === $user->id;
        }
        return false;
    }

    public function create(User $user)
    {
        return $user->hasRole('superadministrator');
    }

    public function update(User $user, Order $order)
    {
        if ($user->hasRole('superadministrator')) {
            return true;
        }
        if ($user->hasRole('merchant')) {
            return $order->merchant_id === $user->id;
        }
        return false;
    }

    public function delete(User $user, Order $order)
    {
        return $user->hasRole('superadministrator');
    }

    public function restore(User $user, Order $order)
    {
        return $user->hasRole('superadministrator');
    }
}
