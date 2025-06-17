<?php

namespace Database\Seeders;

use App\Models\ItemRequest;
use Illuminate\Database\Seeder;

class ItemRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Create item requests
        ItemRequest::factory()->count(30)->create();
    }
} 