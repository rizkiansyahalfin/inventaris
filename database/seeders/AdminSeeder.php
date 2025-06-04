<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pesantren.id',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'is_admin' => true,
        ]);

        // Create additional admin users using factory
        User::factory()->count(3)->create([
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
    }
} 