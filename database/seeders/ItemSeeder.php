<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use Carbon\Carbon;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            // Elektronik
            [
                'name' => 'Laptop Dell XPS 15',
                'description' => 'Laptop Dell XPS 15 dengan spesifikasi tinggi untuk kebutuhan desain dan programming.',
                'quantity' => 3,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Ruang IT',
                'purchase_price' => 25000000,
                'purchase_date' => Carbon::now()->subMonths(6),
                'category' => 'Elektronik'
            ],
            [
                'name' => 'Printer HP LaserJet Pro',
                'description' => 'Printer laser untuk kebutuhan cetak dokumen kantor.',
                'quantity' => 2,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Ruang Admin',
                'purchase_price' => 3500000,
                'purchase_date' => Carbon::now()->subMonths(3),
                'category' => 'Elektronik'
            ],
            // Furnitur
            [
                'name' => 'Meja Kerja Adjustable',
                'description' => 'Meja kerja dengan tinggi yang dapat diatur.',
                'quantity' => 5,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Gudang A',
                'purchase_price' => 1500000,
                'purchase_date' => Carbon::now()->subMonths(2),
                'category' => 'Furnitur'
            ],
            [
                'name' => 'Kursi Ergonomis',
                'description' => 'Kursi kantor ergonomis untuk kenyamanan kerja.',
                'quantity' => 8,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Gudang A',
                'purchase_price' => 1200000,
                'purchase_date' => Carbon::now()->subMonths(2),
                'category' => 'Furnitur'
            ],
            // Alat Tulis
            [
                'name' => 'Stapler Max HD-10',
                'description' => 'Stapler kantor dengan kapasitas besar.',
                'quantity' => 10,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Ruang Admin',
                'purchase_price' => 75000,
                'purchase_date' => Carbon::now()->subMonths(1),
                'category' => 'Alat Tulis'
            ],
            // Perlengkapan Jaringan
            [
                'name' => 'Router TP-Link Archer C6',
                'description' => 'Router WiFi dual band untuk jaringan kantor.',
                'quantity' => 2,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Ruang IT',
                'purchase_price' => 850000,
                'purchase_date' => Carbon::now()->subMonths(4),
                'category' => 'Perlengkapan Jaringan'
            ],
            // Perlengkapan Audio Visual
            [
                'name' => 'Proyektor Epson EB-S41',
                'description' => 'Proyektor untuk presentasi dan meeting.',
                'quantity' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'location' => 'Ruang Meeting',
                'purchase_price' => 4500000,
                'purchase_date' => Carbon::now()->subMonths(5),
                'category' => 'Perlengkapan Audio Visual'
            ],
        ];

        foreach ($items as $itemData) {
            $category = Category::where('name', $itemData['category'])->first();
            if (!$category) continue;

            $item = Item::create([
                'name' => $itemData['name'],
                'description' => $itemData['description'],
                'quantity' => $itemData['quantity'],
                'condition' => $itemData['condition'],
                'status' => $itemData['status'],
                'location' => $itemData['location'],
                'purchase_price' => $itemData['purchase_price'],
                'purchase_date' => $itemData['purchase_date'],
            ]);

            $item->categories()->attach($category->id);
            
            // Generate kode item
            $item->code = $this->generateItemCode($item);
            $item->save();
        }
    }

    private function generateItemCode(Item $item): string
    {
        $primaryCategory = $item->categories()->first();
        if (!$primaryCategory) return "NO-CAT-" . time();

        $categoryCode = $primaryCategory->code;
        $dateCode = $item->purchase_date->format('ym');
        $codePrefix = "{$categoryCode}/{$dateCode}/";

        $latestItem = Item::where('code', 'like', $codePrefix . '%')
            ->where('id', '!=', $item->id)
            ->orderBy('code', 'desc')
            ->first();

        $sequence = 1;
        if ($latestItem) {
            $lastSequence = (int) substr($latestItem->code, -3);
            $sequence = $lastSequence + 1;
        }
        
        return $codePrefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
} 