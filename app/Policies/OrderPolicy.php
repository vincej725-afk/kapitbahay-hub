<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the order.
     * Fix B for Problem M2 (Prevents IDOR)
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->role === 'owner') {
            return true;
        }

        if ($user->role === 'staff') {
            return $user->branch_id === $order->branch_id;
        }

        return $user->id === $order->customer_id;
    }

    /**
     * Determine whether the user can update the order status.
     * Question 3(b)
     */
    public function updateStatus(User $user, Order $order): bool
    {
        if ($user->role === 'owner') {
            return true;
        }

        return $user->role === 'staff'
            && $user->branch_id === $order->branch_id;
    }
}
