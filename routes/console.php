<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:create-initial-super-admin', function () {
    $this->call('db:seed', ['--class' => RolePermissionSeeder::class, '--force' => true]);

    $name = $this->ask('Full name');
    $email = $this->ask('Email address');
    $password = $this->secret('Password (minimum 12 characters)');
    $confirmation = $this->secret('Confirm password');

    $validator = Validator::make(compact('name', 'email', 'password'), [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:12'],
    ]);

    if ($validator->fails() || $password !== $confirmation) {
        $this->error($password !== $confirmation ? 'Passwords do not match.' : $validator->errors()->first());
        return Command::FAILURE;
    }

    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
    ]);

    $user->assignRole(Role::findByName('super admin'));
    $this->info("Super administrator created for {$user->email}.");

    return Command::SUCCESS;
})->purpose('Interactively create the first Home Pearls super administrator');
