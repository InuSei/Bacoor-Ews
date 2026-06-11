<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void { }

    public function boot(): void
    {
        if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        if (Schema::hasTable('users')) {
            if (!User::where('username', 'admin')->exists()) {
                User::create([
                    'full_name'    => 'System Administrator',
                    'username'     => 'admin',
                    'password'     => Hash::make('password123'),
                    'email'        => 'admin@gmail.com',
                    'phone_number' => '09999999999',
                    'role'         => 'admin',
                    'barangay'     => 'Brgy. Bayanan',
                ]);
            }
        }
    }
}