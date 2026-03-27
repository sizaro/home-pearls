<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        return match (true) {
            $user->hasRole('super admin') => redirect()->route('admin.dashboard'),
            default => redirect()->route('admin.dashboard'),
        };
    }
}