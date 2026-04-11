<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        return match (true) {

            // 🔥 SUPER ADMIN
            $user->hasRole('super admin') => redirect()->route('admin.dashboard'),

            // 🔥 ADMIN
            $user->hasRole('admin') => redirect()->route('admin.dashboard'),

            // 🔥 EMPLOYEE
            $user->hasRole('employee') => redirect()->route('admin.dashboard'),

            // 🔥 CUSTOMER (PUBLIC SIDE)
            $user->hasRole('customer') => redirect()->route('home-pearls'),

            // 🔥 fallback (just in case)
            default => redirect('/'),
        };
    }
}