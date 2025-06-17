<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Elektronik',
                'code' => 'ELK',
                'description' => 'Perangkat elektronik dan gadget',
            ],
            [
                'name' => 'Furnitur',
                'code' => 'FRN',
                'description' => 'Peralatan dan perabotan kantor',
            ],
            [
                'name' => 'Alat Tulis Kantor',
                'code' => 'ATK',
                'description' => 'Peralatan tulis dan perlengkapan kantor',
            ],
            [
                'name' => 'Komputer & Aksesoris',
                'code' => 'KMP',
                'description' => 'Perangkat komputer dan aksesorinya',
            ],
            [
                'name' => 'Proyektor & Layar',
                'code' => 'PRJ',
                'description' => 'Perangkat presentasi dan layar',
            ],
        ];

        // Create predefined categories
        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['code' => $category['code']],
                $category
            );
        }

        // Create additional random categories if count is less than 10
        $existingCount = Category::count();
        if ($existingCount < 10) {
            $neededCount = 10 - $existingCount;
            for ($i = 0; $i < $neededCount; $i++) {
                Category::factory()->create();
            }
        }
    }
} 