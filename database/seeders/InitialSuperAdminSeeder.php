<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class InitialSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! config('initial-admin.enabled')) {
            return;
        }

        $details = [
            'name' => config('initial-admin.name'),
            'email' => config('initial-admin.email'),
            'password' => config('initial-admin.password'),
        ];

        Validator::make($details, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:12'],
        ])->validate();

        $user = User::firstOrCreate(
            ['email' => $details['email']],
            [
                'name' => $details['name'],
                'password' => Hash::make($details['password']),
                'email_verified_at' => now(),
            ],
        );

        $user->assignRole(Role::findByName('super admin'));

        $this->command->info("Initial super administrator is ready for {$user->email}.");
    }
}
