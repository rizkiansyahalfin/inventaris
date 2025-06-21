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
        $query = Item::with(['category'])
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            });

        $items = $query->orderBy('name')->paginate(10);
        $categories = Category::orderBy('name')->get();
        $statuses = Item::getStatuses();

        // Log activity
        $filters = [];
        if ($request->search) $filters[] = 'pencarian: ' . $request->search;
        if ($request->category_id) $filters[] = 'kategori: ' . Category::find($request->category_id)->name;
        if ($request->status) $filters[] = 'status: ' . $request->status;
        
        $filterDescription = !empty($filters) ? 'Lihat daftar barang dengan filter: ' . implode(', ', $filters) : 'Lihat daftar barang';
        \App\Models\ActivityLog::log('view', 'item', $filterDescription . ' (' . $items->total() . ' item)');

        return view('items.index', compact('items', 'categories', 'statuses'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $statuses = Item::getStatuses();
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'item', 'Akses halaman tambah barang baru');
        
        return view('items.create', compact('categories', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:1',
            'condition' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|string|in:Tersedia,Dipinjam,Dalam Perbaikan,Rusak,Hilang',
            'location' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
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
            
            // Generate kode dasar
            $mainItem->code = $this->generateItemCode($mainItem);
            $mainItem->save();
            
            // Set item untuk response
            $item = $mainItem;
            
            // Jika stock lebih dari 1, buat record terpisah untuk setiap unit
            if ($validated['stock'] > 1) {
                // Simpan item induk sebagai item pertama dengan kode unit -001
                $baseCode = $mainItem->code;
                $mainItem->code = $baseCode . '-001';
                $mainItem->stock = 1; // Set stock ke 1 untuk item induk
                $mainItem->save();
                
                $createdItems[] = $mainItem;
                $unitCodes[] = $mainItem->code;
                
                // Buat item-item turunan
                for ($i = 2; $i <= $validated['stock']; $i++) {
                    $childItem = new Item();
                    $childItem->fill(array_merge($validated, [
                        'stock' => 1, // Set stock ke 1 untuk setiap unit
                        'image' => $imagePath,
                        'code' => $baseCode . '-' . str_pad($i, 3, '0', STR_PAD_LEFT)
                    ]));
                    $childItem->save();
                    
                    $createdItems[] = $childItem;
                    $unitCodes[] = $childItem->code;
                }
            } else {
                // Jika stock hanya 1, gunakan item yang sudah dibuat
                $mainItem->stock = 1;
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

        // Log activity
        \App\Models\ActivityLog::log('create', 'item', 'Menambah barang baru: ' . $validated['name'] . ' (' . count($unitCodes) . ' unit)');

        return redirect()
            ->route('items.show', $item)
            ->with('success', $message);
    }

    public function show(Item $item)
    {
        $item->load(['category', 'attachments', 'borrows.user'])->loadCount('borrows');
        
        // Cari semua unit dengan kode dasar yang sama
        $baseCode = preg_replace('/-\d+$/', '', $item->code);
        $relatedItems = Item::where(function($query) use ($baseCode) {
                $query->where('code', 'like', $baseCode . '-%')
                    ->orWhere('code', $baseCode);
            })
            ->orderBy('code')
            ->get();
            
        $unitCodes = $relatedItems->pluck('code')->toArray();
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'item', 'Lihat detail barang: ' . $item->name . ' (Kode: ' . $item->code . ')');
        
        return view('items.show', compact('item', 'unitCodes', 'relatedItems'));
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $statuses = Item::getStatuses();
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'item', 'Akses halaman edit barang: ' . $item->name . ' (Kode: ' . $item->code . ')');
        
        return view('items.edit', compact('item', 'categories', 'statuses'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:1',
            'condition' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|string|in:Tersedia,Dipinjam,Dalam Perbaikan,Rusak,Hilang',
            'location' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Periksa apakah kode perlu dibuat ulang
        $purchaseDateChanged = Carbon::parse($validated['purchase_date'])->notEqualTo($item->purchase_date);
        $categoriesChanged = $validated['category_id'] != $item->category_id;
        
        $regenerateCode = $purchaseDateChanged || $categoriesChanged;
        $oldStock = $item->stock;
        $newStock = $validated['stock'];
        $imagePath = $item->image;
        $unitCodes = [];
        $createdItems = [];

        DB::transaction(function() use ($request, $item, $validated, $regenerateCode, $oldStock, $newStock, &$imagePath, &$unitCodes, &$createdItems) {
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

            if ($regenerateCode) {
                // Muat ulang item untuk mendapatkan relasi terbaru
                $item->refresh(); 
                $item->code = $this->generateItemCode($item);
                $item->save();
            }
            
            // Ambil kode dasar
            $baseCode = preg_replace('/-\d+$/', '', $item->code);
            
            // Jika jumlah berubah, perbarui atau buat record baru
            if ($newStock != $oldStock) {
                // Cari item-item yang memiliki kode dasar yang sama
                $relatedItems = Item::where('code', 'like', $baseCode . '-%')
                    ->orWhere('code', $baseCode)
                    ->get();
                
                // Jika jumlah baru lebih besar, tambahkan item baru
                if ($newStock > $relatedItems->count()) {
                    // Cari nomor unit tertinggi yang pernah ada
                    $highestUnitNumber = Item::where('code', 'like', $baseCode . '-%')
                        ->orderByRaw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED) DESC')
                        ->value(DB::raw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED)'));
                    
                    // Jika tidak ada unit sebelumnya, mulai dari 1
                    $startUnitNumber = $highestUnitNumber ? $highestUnitNumber + 1 : 1;
                    
                    // Hitung berapa banyak item yang perlu ditambahkan
                    $itemsToAdd = $newStock - $relatedItems->count();
                    
                    for ($i = 0; $i < $itemsToAdd; $i++) {
                        $newItem = new Item();
                        $newItem->fill(array_merge($validated, [
                            'stock' => 1,
                            'image' => $imagePath,
                            'code' => $baseCode . '-' . str_pad($startUnitNumber + $i, 3, '0', STR_PAD_LEFT)
                        ]));
                        $newItem->save();
                        
                        $createdItems[] = $newItem;
                        $unitCodes[] = $newItem->code;
                    }
                } elseif ($newStock < $relatedItems->count()) {
                    // Jika jumlah baru lebih kecil, hapus item yang berlebih
                    $sortedItems = $relatedItems->sortBy('code');
                    $itemsToRemove = $sortedItems->slice($newStock);
                    
                    foreach ($itemsToRemove as $itemToRemove) {
                            $itemToRemove->delete();
                    }
                }
                
                // Update stock item utama
                $item->stock = $newStock;
                $item->save();
            }
            
            // Pastikan status sesuai dengan kondisi
            if ($item->condition !== 'Baik' && $item->status === Item::STATUS_AVAILABLE) {
                $item->updateStatusFromCondition();
                $item->save();
            }
        });
        
        $message = 'Barang berhasil diperbarui';
        if ($newStock !== $oldStock || $regenerateCode) {
            $message .= ' dengan perubahan kode unit';
        }

        // Log activity
        \App\Models\ActivityLog::log('update', 'item', 'Mengedit barang: ' . $item->name . ' (Kode: ' . $item->code . ')');

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
        \App\Models\ActivityLog::log('delete', 'barang', 'Menghapus barang: ' . $item->name . ' (ID: ' . $item->id . ')');

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus');
    }

    public function showAddStockForm(Item $item)
    {
        // Log activity
        \App\Models\ActivityLog::log('view', 'item', 'Akses halaman tambah stok barang: ' . $item->name . ' (Kode: ' . $item->code . ')');
        
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
            $oldStock = $item->stock;
            $newStock = $oldStock + $validated['quantity_to_add'];
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
            
            // Cari nomor unit tertinggi yang pernah ada, termasuk yang sudah dihapus
            $highestUnitNumber = Item::where('code', 'like', $baseCode . '-%')
                ->orderByRaw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED) DESC')
                ->value(DB::raw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED)'));
                
            // Jika tidak ada unit sebelumnya, mulai dari 1
            $startUnitNumber = $highestUnitNumber ? $highestUnitNumber + 1 : 1;
            
            // Buat record baru untuk setiap unit yang ditambahkan
            for ($i = 0; $i < $validated['quantity_to_add']; $i++) {
                $newItem = new Item();
                $newItem->fill([
                    'name' => $item->name,
                    'description' => $item->description,
                    'stock' => 1,
                    'condition' => $validated['condition'],
                    'location' => $item->location,
                    'purchase_price' => $validated['purchase_price'] ?? $item->purchase_price,
                    'purchase_date' => $validated['purchase_date'] ?? now(),
                    'image' => $item->image,
                    'notes' => $newNotes,
                    'category_id' => $item->category_id,
                ]);
                
                // Set status berdasarkan kondisi
                if ($validated['condition'] === 'Baik') {
                    $newItem->status = Item::STATUS_AVAILABLE;
                } else {
                    $newItem->status = $validated['condition'] === 'Rusak Ringan' ? 
                        Item::STATUS_MAINTENANCE : Item::STATUS_DAMAGED;
                }
                
                // Generate kode unit baru
                $unitNumber = $startUnitNumber + $i;
                $newItem->code = $baseCode . '-' . str_pad($unitNumber, 3, '0', STR_PAD_LEFT);
                
                $newItem->save();
                
                $newUnitCodes[] = $newItem->code;
            }
            
            // Update catatan pada item utama
            $item->notes = $item->notes 
                ? $item->notes . "\n\n" . $newNotes 
                : $newNotes;
            
            // Update jumlah total pada item utama
            $item->stock = $newStock;
            $item->save();
            
            DB::commit();
            
            \App\Models\ActivityLog::log('add_stock', 'barang', 'Menambah stok barang: ' . $item->name . ' (ID: ' . $item->id . '), jumlah: ' . $validated['quantity_to_add']);
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
        $primaryCategory = $item->category;
        if (!$primaryCategory) {
            // Fallback jika tidak ada kategori
            return "NO-CAT-" . time(); 
        }
        $categoryCode = $primaryCategory->code;

        // 2. Ambil Kode Tanggal Pembelian
        $dateCode = $item->purchase_date->format('ym');

        // 3. Cari Urutan
        $codePrefix = "{$categoryCode}/{$dateCode}/";
        
        // Cari kode tertinggi yang pernah ada, termasuk yang sudah dihapus
        // Menggunakan withTrashed() jika menggunakan soft deletes
        $latestItem = Item::where(function($query) use ($codePrefix) {
                // Cari kode dengan prefix yang sama
                $query->where('code', 'like', $codePrefix . '%');
                
                // Juga cari kode unit dengan prefix yang sama
                $query->orWhere('code', 'like', $codePrefix . '%-___');
            })
            ->where('id', '!=', $item->id) // Kecuali item ini
            ->orderByRaw('LENGTH(code) DESC, code DESC') // Urutkan berdasarkan panjang dan nilai
            ->first();

        $sequence = 1;
        if ($latestItem) {
            // Ekstrak nomor urut dari kode
            $code = $latestItem->code;
            
            // Jika kode memiliki format dengan unit (contoh: ELK/2506/002-001)
            if (strpos($code, '-') !== false) {
                // Ambil bagian sebelum tanda "-"
                $code = explode('-', $code)[0];
            }
            
            // Ambil 3 karakter terakhir untuk mendapatkan nomor urut
            $lastSequence = (int) substr($code, -3);
            $sequence = $lastSequence + 1;
        }
        
        return $codePrefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
} 