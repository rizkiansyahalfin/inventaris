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
        $categories = Category::all();
        $conditions = ['Baik', 'Rusak Ringan', 'Rusak Berat'];
        $statuses = ['Tersedia', 'Dipinjam'];

        for ($i = 0; $i < 30; $i++) {
            $itemCategory = $categories->random();
            $purchaseDate = Carbon::now()->subMonths(rand(1, 24));
            
            // Temporary item to generate code
            $tempItemData = [
                'name' => 'Barang Dummy ' . ($i + 1),
                'description' => 'Ini adalah deskripsi untuk barang dummy.',
                'condition' => $conditions[array_rand($conditions)],
                'status' => 'Tersedia', // Default to available first
                'location' => 'Gudang ' . rand(1, 5),
                'purchase_price' => rand(50000, 2000000),
                'purchase_date' => $purchaseDate,
                'code' => 'TEMP' // Temporary code
            ];

            $item = Item::create($tempItemData);
            $item->categories()->attach($itemCategory->id);
            
            // Generate real code
            $item->code = $this->generateItemCode($item);
            $item->save();
        }
    }

    private function generateItemCode(Item $item): string
    {
        $primaryCategory = $item->categories()->first();
        if (!$primaryCategory) return "NO-CAT-" . time(); 

        // Access the new attribute from the Category model
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