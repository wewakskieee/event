<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin EventTix',
            'email' => 'admin@eventtix.com',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Regular Users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '081234567891',
            'role' => 'user',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '081234567892',
            'role' => 'user',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
