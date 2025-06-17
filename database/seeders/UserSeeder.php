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
        // Create admin user if not exists
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        // Create petugas user if not exists
        if (!User::where('email', 'petugas@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Petugas',
                'email' => 'petugas@example.com',
                'password' => Hash::make('password'),
                'role' => 'petugas',
            ]);
        }

        // Create regular user if not exists
        if (!User::where('email', 'user@example.com')->exists()) {
            User::factory()->create([
                'name' => 'User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]);
        }

        // Create additional random users if count is less than 5
        $randomUserCount = User::whereNotIn('email', [
            'admin@example.com',
            'petugas@example.com',
            'user@example.com'
        ])->count();

        if ($randomUserCount < 5) {
            User::factory()->count(5 - $randomUserCount)->create();
        }
    }
}
