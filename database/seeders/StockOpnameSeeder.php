<?php

namespace Database\Seeders;

use App\Models\StockOpname;
use Illuminate\Database\Seeder;

class StockOpnameSeeder extends Seeder
{
    public function run(): void
    {
        // Create stock opnames
        StockOpname::factory()->count(20)->create();
    }
} 