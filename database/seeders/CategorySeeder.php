<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create(['name' => 'Alat Tulis Kantor']);
        Category::create(['name' => 'Elektronik']);
        Category::create(['name' => 'Perlengkapan Ibadah']);
        Category::create(['name' => 'Furnitur']);
        Category::create(['name' => 'Peralatan Kebersihan']);
    }
} 