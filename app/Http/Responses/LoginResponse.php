<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/login');
        }

        return match (true) {

            $user->hasRole('super admin') => redirect()->route('admin.dashboard'),

            $user->hasRole('admin') => redirect()->route('admin.dashboard'),

            $user->hasRole('employee') => redirect()->route('admin.dashboard'),

            $user->hasRole('customer') => redirect()->route('home-pearls'),

            default => redirect()->route('home-pearls'),
        };
    }
}