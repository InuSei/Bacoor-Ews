<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barangay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Generate the Master System Administrator
        User::create([
            'full_name'    => 'System Administrator',
            'username'     => 'admin', // Required by client DB
            'password'     => Hash::make('password123'), // Securely hashed
            'email'        => 'admin@gmail.com',
            'phone_number' => '09999999999',
            'role'         => 'admin',
            'barangay'     => 'Brgy. Bayanan', // Text string, matching new DB
        ]);

    }
}