<?php

namespace App\Policies;

use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductVariantPolicy
{
    public function create(User $user): bool
{
    return $user->hasAnyRole(['super admin', 'admin']);
}

public function update(User $user, ProductVariant $variant): bool
{
    if ($user->hasRole('super admin')) return true;

    return $variant->product->created_by === $user->id;
}

public function delete(User $user, ProductVariant $variant): bool
{
    if ($user->hasRole('super admin')) return true;

    return $variant->product->created_by === $user->id;
}
}
