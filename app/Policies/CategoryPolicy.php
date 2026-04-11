<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    public function create(User $user): bool
{
    return $user->hasAnyRole(['super admin', 'admin']);
}

public function update(User $user, Category $category): bool
{
    if ($user->hasRole('super admin')) return true;

    return $category->created_by === $user->id;
}

public function delete(User $user, Category $category): bool
{
    if ($user->hasRole('super admin')) return true;

    return $category->created_by === $user->id;
}
    }
