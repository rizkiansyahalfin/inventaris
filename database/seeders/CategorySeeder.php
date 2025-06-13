<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Elektronik',
                'code' => 'ELK',
                'description' => 'Perangkat elektronik seperti laptop, komputer, printer, dll.',
            ],
            [
                'name' => 'Furnitur',
                'code' => 'FRN',
                'description' => 'Perabotan kantor seperti meja, kursi, lemari, dll.',
            ],
            [
                'name' => 'Alat Tulis',
                'code' => 'AT',
                'description' => 'Peralatan tulis menulis dan kantor.',
            ],
            [
                'name' => 'Kendaraan',
                'code' => 'KDR',
                'description' => 'Kendaraan operasional kantor.',
            ],
            [
                'name' => 'Perlengkapan Kebersihan',
                'code' => 'PB',
                'description' => 'Alat-alat kebersihan dan pembersih.',
            ],
            [
                'name' => 'Perlengkapan Keamanan',
                'code' => 'PKM',
                'description' => 'Alat-alat keamanan dan keselamatan.',
            ],
            [
                'name' => 'Perlengkapan Jaringan',
                'code' => 'PJ',
                'description' => 'Perangkat jaringan dan komunikasi.',
            ],
            [
                'name' => 'Perlengkapan Audio Visual',
                'code' => 'PAV',
                'description' => 'Perangkat audio dan visual untuk presentasi.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'code' => $category['code'],
                'description' => $category['description'],
            ]);
        }
    }
} 