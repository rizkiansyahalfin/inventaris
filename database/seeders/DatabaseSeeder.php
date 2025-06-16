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
            UserSeeder::class, // Seeder user baru dengan 3 role
            CategorySeeder::class,
            ItemSeeder::class,
            BorrowSeeder::class,
            MaintenanceSeeder::class,
        ]);
    }
}
