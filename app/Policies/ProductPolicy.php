<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // everyone logged in can view list (adjust if needed)
    }

    public function view(User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super admin', 'admin']);
    }

    public function update(User $user, Product $product): bool
    {
        if ($user->hasRole('super admin')) {
            return true;
        }

        return $user->hasRole('admin') &&
               $product->created_by === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        if ($user->hasRole('super admin')) {
            return true;
        }

        return $user->hasRole('admin') &&
               $product->created_by === $user->id;
    }
}