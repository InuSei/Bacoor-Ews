<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

public function boot(): void
{
    // Ensure the users table exists before trying to create an admin
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
}
