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
    public function run(): void
    {
        // Create items for each category
        Item::factory()->count(20)->create();
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