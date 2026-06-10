<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Add these imports at the top!
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Check for table before doing anything else
        if (Schema::hasTable('users')) {
            $adminExists = User::where('username', 'admin')->exists();
            
            if (!$adminExists) {
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
    } // <--- THIS BRACKET CLOSES BOOT
} // <--- THIS BRACKET CLOSES THE CLASS