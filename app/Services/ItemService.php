<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Category;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    /**
     * Create an item (with multi-unit support).
     *
     * @return array{item: Item, unitCodes: string[]}
     */
    public function createItem(array $validated, $imageFile = null): array
    {
        $item = null;
        $unitCodes = [];

        DB::transaction(function () use ($validated, $imageFile, &$item, &$unitCodes) {
            $imagePath = null;
            if ($imageFile) {
                $path = $imageFile->store('items', 'public');
                $imagePath = basename($path);
            }

            // Create main item
            $mainItem = new Item();
            $mainItem->fill(array_merge($validated, ['image' => $imagePath]));
            $mainItem->save();

            // Generate base code
            $mainItem->code = $this->generateItemCode($mainItem);
            $mainItem->save();

            $item = $mainItem;
            $createdItems = [];

            if ($validated['stock'] > 1) {
                $baseCode = $mainItem->code;
                $mainItem->code = $baseCode . '-001';
                $mainItem->stock = 1;
                $mainItem->save();

                $createdItems[] = $mainItem;
                $unitCodes[] = $mainItem->code;

                for ($i = 2; $i <= $validated['stock']; $i++) {
                    $childItem = new Item();
                    $childItem->fill(array_merge($validated, [
                        'stock' => 1,
                        'image' => $imagePath,
                        'code' => $baseCode . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    ]));
                    $childItem->save();
                    $createdItems[] = $childItem;
                    $unitCodes[] = $childItem->code;
                }
            } else {
                $mainItem->stock = 1;
                $mainItem->save();
                $createdItems[] = $mainItem;
                $unitCodes[] = $mainItem->code;
            }

            // Sync status with condition
            foreach ($createdItems as $createdItem) {
                if ($createdItem->condition !== 'Baik' && $createdItem->status === Item::STATUS_AVAILABLE) {
                    $createdItem->updateStatusFromCondition();
                    $createdItem->save();
                }
            }
        });

        ActivityLog::log('create', 'item', 'Menambah barang baru: ' . $validated['name'] . ' (' . count($unitCodes) . ' unit)');

        return ['item' => $item, 'unitCodes' => $unitCodes];
    }

    /**
     * Update an item (with multi-unit support).
     *
     * @return array{unitCodes: string[], regenerateCode: bool}
     */
    public function updateItem(Item $item, array $validated, $imageFile = null): array
    {
        $purchaseDateChanged = Carbon::parse($validated['purchase_date'])->notEqualTo($item->purchase_date);
        $categoriesChanged = $validated['category_id'] != $item->category_id;
        $regenerateCode = $purchaseDateChanged || $categoriesChanged;
        $oldStock = $item->stock;
        $newStock = $validated['stock'];
        $imagePath = $item->image;
        $unitCodes = [];

        DB::transaction(function () use ($imageFile, $item, $validated, $regenerateCode, $oldStock, $newStock, &$imagePath, &$unitCodes) {
            if ($imageFile) {
                if ($item->image) {
                    Storage::disk('public')->delete('items/' . $item->image);
                }
                $path = $imageFile->store('items', 'public');
                $imagePath = basename($path);
                $validated['image'] = $imagePath;
            }

            $item->update($validated);

            if ($regenerateCode) {
                $item->refresh();
                $item->code = $this->generateItemCode($item);
                $item->save();
            }

            $baseCode = preg_replace('/-\d+$/', '', $item->code);

            if ($newStock != $oldStock) {
                $relatedItems = Item::where('code', 'like', $baseCode . '-%')
                    ->orWhere('code', $baseCode)
                    ->get();

                if ($newStock > $relatedItems->count()) {
                    $highestUnitNumber = Item::where('code', 'like', $baseCode . '-%')
                        ->orderByRaw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED) DESC')
                        ->value(DB::raw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED)'));

                    $startUnitNumber = $highestUnitNumber ? $highestUnitNumber + 1 : 1;
                    $itemsToAdd = $newStock - $relatedItems->count();

                    for ($i = 0; $i < $itemsToAdd; $i++) {
                        $newItem = new Item();
                        $newItem->fill(array_merge($validated, [
                            'stock' => 1,
                            'image' => $imagePath,
                            'code' => $baseCode . '-' . str_pad($startUnitNumber + $i, 3, '0', STR_PAD_LEFT),
                        ]));
                        $newItem->save();
                        $unitCodes[] = $newItem->code;
                    }
                } elseif ($newStock < $relatedItems->count()) {
                    $sortedItems = $relatedItems->sortBy('code');
                    $itemsToRemove = $sortedItems->slice($newStock);

                    foreach ($itemsToRemove as $itemToRemove) {
                        $itemToRemove->delete();
                    }
                }

                $item->stock = $newStock;
                $item->save();
            }

            if ($item->condition !== 'Baik' && $item->status === Item::STATUS_AVAILABLE) {
                $item->updateStatusFromCondition();
                $item->save();
            }
        });

        ActivityLog::log('update', 'item', 'Mengedit barang: ' . $item->name . ' (Kode: ' . $item->code . ')');

        return ['unitCodes' => $unitCodes, 'regenerateCode' => $regenerateCode];
    }

    /**
     * Add stock to an existing item.
     *
     * @return string[]
     */
    public function addStock(Item $item, array $validated): array
    {
        $newUnitCodes = [];

        DB::transaction(function () use ($item, $validated, &$newUnitCodes) {
            $oldStock = $item->stock;
            $newStock = $oldStock + $validated['quantity_to_add'];
            $baseCode = preg_replace('/-\d+$/', '', $item->code);

            $purchaseInfo = '';
            if (!empty($validated['purchase_date'])) {
                $purchaseInfo = " (Tanggal: " . $validated['purchase_date'] . ")";
            }
            if (!empty($validated['purchase_price'])) {
                $purchaseInfo .= " (Harga: Rp" . number_format($validated['purchase_price'], 0, ',', '.') . ")";
            }

            $newNotes = "Penambahan stok " . $validated['quantity_to_add'] . " unit pada " .
                date('Y-m-d H:i:s') . $purchaseInfo . "\n" .
                "Kondisi: " . $validated['condition'];

            if (!empty($validated['notes'])) {
                $newNotes .= "\nCatatan: " . $validated['notes'];
            }

            $highestUnitNumber = Item::where('code', 'like', $baseCode . '-%')
                ->orderByRaw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED) DESC')
                ->value(DB::raw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED)'));

            $startUnitNumber = $highestUnitNumber ? $highestUnitNumber + 1 : 1;

            for ($i = 0; $i < $validated['quantity_to_add']; $i++) {
                $newItem = new Item();
                $newItem->fill([
                    'name' => $item->name,
                    'description' => $item->description,
                    'stock' => 1,
                    'condition' => $validated['condition'],
                    'location_id' => $item->location_id,
                    'purchase_price' => $validated['purchase_price'] ?? $item->purchase_price,
                    'purchase_date' => $validated['purchase_date'] ?? now(),
                    'image' => $item->image,
                    'notes' => $newNotes,
                    'category_id' => $item->category_id,
                ]);

                if ($validated['condition'] === 'Baik') {
                    $newItem->status = Item::STATUS_AVAILABLE;
                } else {
                    $newItem->status = $validated['condition'] === 'Rusak Ringan' ?
                        Item::STATUS_MAINTENANCE : Item::STATUS_DAMAGED;
                }

                $unitNumber = $startUnitNumber + $i;
                $newItem->code = $baseCode . '-' . str_pad($unitNumber, 3, '0', STR_PAD_LEFT);
                $newItem->save();
                $newUnitCodes[] = $newItem->code;
            }

            $item->notes = $item->notes
                ? $item->notes . "\n\n" . $newNotes
                : $newNotes;
            $item->stock = $newStock;
            $item->save();
        });

        ActivityLog::log('add_stock', 'barang', 'Menambah stok barang: ' . $item->name . ' (ID: ' . $item->id . '), jumlah: ' . $validated['quantity_to_add']);

        return $newUnitCodes;
    }

    /**
     * Generate a unique item code.
     */
    public function generateItemCode(Item $item): string
    {
        $primaryCategory = $item->category;
        if (!$primaryCategory) {
            return "NO-CAT-" . time();
        }
        $categoryCode = $primaryCategory->code;
        $dateCode = Carbon::parse($item->purchase_date)->format('ym');
        $codePrefix = "{$categoryCode}/{$dateCode}/";

        $latestItem = Item::where(function ($query) use ($codePrefix) {
            $query->where('code', 'like', $codePrefix . '%');
            $query->orWhere('code', 'like', $codePrefix . '%-___');
        })
            ->where('id', '!=', $item->id)
            ->orderByRaw('LENGTH(code) DESC, code DESC')
            ->first();

        $sequence = 1;
        if ($latestItem) {
            $code = $latestItem->code;
            if (strpos($code, '-') !== false) {
                $code = explode('-', $code)[0];
            }
            $lastSequence = (int) substr($code, -3);
            $sequence = $lastSequence + 1;
        }

        return $codePrefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}
