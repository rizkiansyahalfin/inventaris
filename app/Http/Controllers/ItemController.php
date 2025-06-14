<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['categories'])
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $categoryId) {
                return $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            });

        $items = $query->orderBy('name')->paginate(10);
        $categories = Category::orderBy('name')->get();
        $statuses = Item::getStatuses();

        return view('items.index', compact('items', 'categories', 'statuses'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $statuses = Item::getStatuses();
        return view('items.create', compact('categories', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|string|in:Tersedia,Dipinjam,Dalam Perbaikan,Rusak,Hilang',
            'location' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'required|date',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $item = null;
        $createdItems = [];
        $unitCodes = [];

        DB::transaction(function () use ($validated, $request, &$item, &$createdItems, &$unitCodes) {
            // Proses upload gambar jika ada
            $imagePath = null;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('items', 'public');
                $imagePath = basename($path);
            }
            
            // Buat item induk terlebih dahulu untuk mendapatkan kode dasar
            $mainItem = new Item();
            $mainItem->fill(array_merge($validated, [
                'image' => $imagePath
            ]));
            $mainItem->save();
            $mainItem->categories()->attach($request->category_ids);
            
            // Generate kode dasar
            $mainItem->code = $this->generateItemCode($mainItem);
            $mainItem->save();
            
            // Set item untuk response
            $item = $mainItem;
            
            // Jika quantity lebih dari 1, buat record terpisah untuk setiap unit
            if ($validated['quantity'] > 1) {
                // Simpan item induk sebagai item pertama dengan kode unit -001
                $mainItem->code = $mainItem->code . '-001';
                $mainItem->quantity = 1; // Set quantity ke 1 untuk item induk
                $mainItem->save();
                
                $createdItems[] = $mainItem;
                $unitCodes[] = $mainItem->code;
                
                // Buat item-item turunan
                $baseCode = preg_replace('/-\d+$/', '', $mainItem->code);
                for ($i = 2; $i <= $validated['quantity']; $i++) {
                    $childItem = new Item();
                    $childItem->fill(array_merge($validated, [
                        'quantity' => 1, // Set quantity ke 1 untuk setiap unit
                        'image' => $imagePath,
                        'code' => $baseCode . '-' . str_pad($i, 3, '0', STR_PAD_LEFT)
                    ]));
                    $childItem->save();
                    $childItem->categories()->attach($request->category_ids);
                    
                    $createdItems[] = $childItem;
                    $unitCodes[] = $childItem->code;
                }
            } else {
                // Jika quantity hanya 1, gunakan item yang sudah dibuat
                $mainItem->quantity = 1;
                $mainItem->save();
                
                $createdItems[] = $mainItem;
                $unitCodes[] = $mainItem->code;
            }
            
            // Pastikan status sesuai dengan kondisi untuk semua item
            foreach ($createdItems as $createdItem) {
                if ($createdItem->condition !== 'Baik' && $createdItem->status === Item::STATUS_AVAILABLE) {
                    $createdItem->updateStatusFromCondition();
                    $createdItem->save();
                }
            }
        });

        $message = 'Barang berhasil ditambahkan';
        if (count($unitCodes) > 1) {
            $message .= ' dengan kode unit: ' . implode(', ', array_slice($unitCodes, 0, 3));
            if (count($unitCodes) > 3) {
                $message .= ' dan ' . (count($unitCodes) - 3) . ' lainnya';
            }
        } else {
            $message .= ' dengan kode: ' . $unitCodes[0];
        }

        return redirect()
            ->route('items.show', $item)
            ->with('success', $message);
    }

    public function show(Item $item)
    {
        $item->load(['categories', 'attachments', 'borrows.user'])->loadCount('borrows');
        
        // Cari semua unit dengan kode dasar yang sama
        $baseCode = preg_replace('/-\d+$/', '', $item->code);
        $relatedItems = Item::where(function($query) use ($baseCode) {
                $query->where('code', 'like', $baseCode . '-%')
                    ->orWhere('code', $baseCode);
            })
            ->orderBy('code')
            ->get();
            
        $unitCodes = $relatedItems->pluck('code')->toArray();
        
        return view('items.show', compact('item', 'unitCodes', 'relatedItems'));
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $statuses = Item::getStatuses();
        return view('items.edit', compact('item', 'categories', 'statuses'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|string|in:Tersedia,Dipinjam,Dalam Perbaikan,Rusak,Hilang',
            'location' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'required|date',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Periksa apakah kode perlu dibuat ulang
        $purchaseDateChanged = Carbon::parse($validated['purchase_date'])->notEqualTo($item->purchase_date);
        $categoriesChanged = !empty(array_diff($request->category_ids, $item->categories->pluck('id')->all()));
        
        $regenerateCode = $purchaseDateChanged || $categoriesChanged;
        $oldQuantity = $item->quantity;
        $newQuantity = $validated['quantity'];
        $imagePath = $item->image;
        $unitCodes = [];
        $createdItems = [];

        DB::transaction(function() use ($request, $item, $validated, $regenerateCode, $oldQuantity, $newQuantity, &$imagePath, &$unitCodes, &$createdItems) {
            if ($request->hasFile('image')) {
                if ($item->image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete('items/' . $item->image);
                }
                $path = $request->file('image')->store('items', 'public');
                $imagePath = basename($path);
                $validated['image'] = $imagePath;
            }

            // Update item utama
            $item->update($validated);
            $item->categories()->sync($request->category_ids);

            if ($regenerateCode) {
                // Muat ulang item untuk mendapatkan relasi terbaru
                $item->refresh(); 
                $item->code = $this->generateItemCode($item);
                $item->save();
            }
            
            // Jika jumlah berubah, perbarui atau buat record baru
            if ($newQuantity != $oldQuantity) {
                // Cari item-item yang memiliki kode dasar yang sama
                $baseCode = preg_replace('/-\d+$/', '', $item->code);
                $relatedItems = Item::where('code', 'like', $baseCode . '-%')
                    ->orWhere('code', $baseCode)
                    ->get();
                
                // Jika jumlah baru lebih besar, tambahkan item baru
                if ($newQuantity > $relatedItems->count()) {
                    // Mulai dari nomor urut terakhir + 1
                    $startIndex = $relatedItems->count() + 1;
                    
                    for ($i = $startIndex; $i <= $newQuantity; $i++) {
                        $newItem = new Item();
                        $newItem->fill($validated);
                        $newItem->quantity = 1;
                        $newItem->code = $baseCode . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                        $newItem->save();
                        $newItem->categories()->attach($request->category_ids);
                        
                        $createdItems[] = $newItem;
                        $unitCodes[] = $newItem->code;
                    }
                }
                // Jika jumlah baru lebih kecil, hapus item yang tidak diperlukan
                elseif ($newQuantity < $relatedItems->count()) {
                    // Urutkan item berdasarkan kode
                    $sortedItems = $relatedItems->sortBy('code');
                    
                    // Hapus item dari belakang
                    $itemsToRemove = $sortedItems->slice($newQuantity);
                    foreach ($itemsToRemove as $itemToRemove) {
                        // Pastikan tidak menghapus item yang sedang dipinjam
                        if ($itemToRemove->status !== Item::STATUS_BORROWED) {
                            $itemToRemove->delete();
                        } else {
                            // Jika ada item yang dipinjam, tambahkan catatan
                            $item->notes = $item->notes 
                                ? $item->notes . "\n\nTidak dapat menghapus unit " . $itemToRemove->code . " karena sedang dipinjam."
                                : "Tidak dapat menghapus unit " . $itemToRemove->code . " karena sedang dipinjam.";
                            $item->save();
                        }
                    }
                }
                
                // Perbarui jumlah item utama
                $item->quantity = $newQuantity;
                $item->save();
            }
            
            // Jika ada perubahan kondisi, pastikan status diperbarui kecuali sedang dipinjam
            if ($item->status !== Item::STATUS_BORROWED) {
                $item->updateStatusFromCondition();
                $item->save();
            }
            
            // Simpan kode unit untuk pesan sukses
            $allItems = Item::where('code', 'like', $baseCode . '-%')
                ->orWhere('code', $baseCode)
                ->get();
            
            foreach ($allItems as $relatedItem) {
                $unitCodes[] = $relatedItem->code;
            }
        });
        
        $message = 'Barang berhasil diperbarui';
        
        // Jika jumlah berubah, tampilkan kode unit baru
        if ($newQuantity !== $oldQuantity || $regenerateCode) {
            if (count($unitCodes) > 1) {
                $message .= ' dengan kode unit: ' . implode(', ', array_slice($unitCodes, 0, 3));
                if (count($unitCodes) > 3) {
                    $message .= ' dan ' . (count($unitCodes) - 3) . ' lainnya';
                }
            } else {
                $message .= ' dengan kode: ' . $unitCodes[0];
            }
        }

        return redirect()
            ->route('items.show', $item)
            ->with('success', $message);
    }

    public function destroy(Item $item)
    {
        if ($item->borrows()->where('status', 'borrowed')->exists()) {
            return back()->with('error', 'Tidak dapat menghapus barang yang sedang dipinjam');
        }

        if ($item->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete('items/' . $item->image);
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus');
    }

    public function showAddStockForm(Item $item)
    {
        return view('items.add-stock', compact('item'));
    }

    public function addStock(Request $request, Item $item)
    {
        $validated = $request->validate([
            'quantity_to_add' => 'required|integer|min:1',
            'condition' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'notes' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            
            // Simpan jumlah lama untuk menghitung kode unit baru
            $oldQuantity = $item->quantity;
            $newQuantity = $oldQuantity + $validated['quantity_to_add'];
            $baseCode = preg_replace('/-\d+$/', '', $item->code);
            $newUnitCodes = [];
            
            // Siapkan catatan untuk penambahan stok
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
            
            // Cari item-item yang sudah ada dengan kode dasar yang sama
            $existingItems = Item::where('code', 'like', $baseCode . '-%')
                ->orWhere('code', $baseCode)
                ->get();
            
            // Hitung nomor urut terakhir
            $lastIndex = $existingItems->count();
            
            // Buat record baru untuk setiap unit yang ditambahkan
            for ($i = 1; $i <= $validated['quantity_to_add']; $i++) {
                $newItem = new Item();
                $newItem->fill([
                    'name' => $item->name,
                    'description' => $item->description,
                    'quantity' => 1,
                    'condition' => $validated['condition'],
                    'location' => $item->location,
                    'purchase_price' => $validated['purchase_price'] ?? $item->purchase_price,
                    'purchase_date' => $validated['purchase_date'] ?? now(),
                    'image' => $item->image,
                    'notes' => $newNotes,
                ]);
                
                // Set status berdasarkan kondisi
                if ($validated['condition'] === 'Baik') {
                    $newItem->status = Item::STATUS_AVAILABLE;
                } else {
                    $newItem->status = $validated['condition'] === 'Rusak Ringan' ? 
                        Item::STATUS_MAINTENANCE : Item::STATUS_DAMAGED;
                }
                
                // Generate kode unit baru
                $newIndex = $lastIndex + $i;
                $newItem->code = $baseCode . '-' . str_pad($newIndex, 3, '0', STR_PAD_LEFT);
                
                $newItem->save();
                $newItem->categories()->attach($item->categories->pluck('id')->toArray());
                
                $newUnitCodes[] = $newItem->code;
            }
            
            // Update catatan pada item utama
            $item->notes = $item->notes 
                ? $item->notes . "\n\n" . $newNotes 
                : $newNotes;
            
            // Update jumlah total pada item utama
            $item->quantity = $newQuantity;
            $item->save();
            
            DB::commit();
            
            $message = 'Penambahan stok berhasil sebanyak ' . $validated['quantity_to_add'] . ' unit';
            if (count($newUnitCodes) > 0) {
                $message .= ' dengan kode unit baru: ' . implode(', ', array_slice($newUnitCodes, 0, 3));
                if (count($newUnitCodes) > 3) {
                    $message .= ' dan ' . (count($newUnitCodes) - 3) . ' lainnya';
                }
            }
            
            return redirect()
                ->route('items.show', $item)
                ->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambah stok: ' . $e->getMessage());
        }
    }

    private function generateItemCode(Item $item): string
    {
        // 1. Ambil Kode Kategori
        $primaryCategory = $item->categories()->first();
        if (!$primaryCategory) {
            // Fallback jika tidak ada kategori
            return "NO-CAT-" . time(); 
        }
        $categoryCode = $primaryCategory->code;

        // 2. Ambil Kode Tanggal Pembelian
        $dateCode = $item->purchase_date->format('ym');

        // 3. Cari Urutan
        $codePrefix = "{$categoryCode}/{$dateCode}/";
        
        $latestItem = Item::where('code', 'like', $codePrefix . '%')
            ->where('id', '!=', $item->id) // Kecuali item ini
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