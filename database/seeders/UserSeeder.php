<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Petugas users
        User::create([
            'name' => 'Petugas 1',
            'email' => 'petugas1@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        User::create([
            'name' => 'Petugas 2',
            'email' => 'petugas2@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        // Regular users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'user',
            ]);
        }
    }
}
