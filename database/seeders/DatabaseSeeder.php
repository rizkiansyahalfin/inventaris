<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create a default regular user for testing
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_admin' => false,
        ]);

        $this->call([
            AdminSeeder::class, // Call the new AdminSeeder
            CategorySeeder::class,
            ItemSeeder::class,
            BorrowSeeder::class,
            MaintenanceSeeder::class,
            // You can add BorrowSeeder and MaintenanceSeeder here if you create them
        ]);
    }
}
