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
                'name' => 'Kitab & Buku',
                'code' => 'KTB',
                'description' => 'Kitab kuning, buku pelajaran, dan literatur keislaman',
            ],
            [
                'name' => 'Alat Tulis',
                'code' => 'ATK',
                'description' => 'Peralatan tulis dan perlengkapan belajar',
            ],
            [
                'name' => 'Elektronik',
                'code' => 'ELK',
                'description' => 'Perangkat elektronik dan gadget',
            ],
            [
                'name' => 'Furnitur',
                'code' => 'FRN',
                'description' => 'Peralatan dan perabotan pondok',
            ],
            [
                'name' => 'Komputer & Aksesoris',
                'code' => 'KMP',
                'description' => 'Perangkat komputer dan aksesorinya',
            ],
            [
                'name' => 'Proyektor & Audio',
                'code' => 'PRJ',
                'description' => 'Perangkat presentasi dan audio visual',
            ],
            [
                'name' => 'Peralatan Dapur',
                'code' => 'DKP',
                'description' => 'Peralatan dapur dan memasak',
            ],
            [
                'name' => 'Peralatan Kebersihan',
                'code' => 'KBR',
                'description' => 'Peralatan kebersihan dan sanitasi',
            ],
            [
                'name' => 'Peralatan Olahraga',
                'code' => 'OLG',
                'description' => 'Peralatan olahraga dan rekreasi',
            ],
            [
                'name' => 'Peralatan Kesehatan',
                'code' => 'KSH',
                'description' => 'Peralatan kesehatan dan P3K',
            ],
            [
                'name' => 'Peralatan Pertukangan',
                'code' => 'TKN',
                'description' => 'Peralatan pertukangan dan maintenance',
            ],
            [
                'name' => 'Peralatan Transportasi',
                'code' => 'TRP',
                'description' => 'Kendaraan dan peralatan transportasi',
            ],
        ];

        // Create predefined categories
        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
} 