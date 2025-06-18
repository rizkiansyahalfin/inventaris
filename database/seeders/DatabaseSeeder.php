<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Basic data first
            UserSeeder::class,
            CategorySeeder::class,
            SystemConfigSeeder::class,
            
            // Items and related data
            ItemSeeder::class,
            MaintenanceSeeder::class,
            
            // Borrowing data
            BorrowSeeder::class,
            ItemRequestSeeder::class,
            
            // System data
            StockOpnameSeeder::class,
            ActivityLogSeeder::class,
            
            // Dummy data for testing (optional)
            // DummyDataSeeder::class,
        ]);
    }
}
