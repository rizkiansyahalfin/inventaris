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
        $unitCodes = [];

        DB::transaction(function () use ($validated, $request, &$item, &$unitCodes) {
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('items', 'public');
                $validated['image'] = basename($path);
            }
    
            // Buat item tanpa kode terlebih dahulu
            $item = Item::create($validated);
            $item->categories()->attach($request->category_ids);

            // Generate kode dasar
            $item->code = $this->generateItemCode($item);
            $item->save();

            // Generate kode unit jika jumlah > 1
            if ($item->quantity > 1) {
                $unitCodes = $item->generateUnitCodes();
            } else {
                $unitCodes = [$item->code];
            }
            
            // Pastikan status sesuai dengan kondisi
            if ($item->condition !== 'Baik' && $item->status === Item::STATUS_AVAILABLE) {
                $item->updateStatusFromCondition();
                $item->save();
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
        $unitCodes = $item->generateUnitCodes();
        return view('items.show', compact('item', 'unitCodes'));
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

        DB::transaction(function() use ($request, $item, $validated, $regenerateCode, $oldQuantity) {
            if ($request->hasFile('image')) {
                if ($item->image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete('items/' . $item->image);
                }
                $path = $request->file('image')->store('items', 'public');
                $validated['image'] = basename($path);
            }

            $item->update($validated);
            $item->categories()->sync($request->category_ids);

            if ($regenerateCode) {
                // Muat ulang item untuk mendapatkan relasi terbaru
                $item->refresh(); 
                $item->code = $this->generateItemCode($item);
                $item->save();
            }
            
            // Jika ada perubahan kondisi, pastikan status diperbarui kecuali sedang dipinjam
            if ($item->status !== Item::STATUS_BORROWED) {
                $item->updateStatusFromCondition();
                $item->save();
            }
        });
        
        $message = 'Barang berhasil diperbarui';
        
        // Jika jumlah berubah, tampilkan kode unit baru
        if ($item->quantity !== $oldQuantity || $regenerateCode) {
            $unitCodes = $item->generateUnitCodes();
            
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
            
            // Update jumlah barang
            $item->quantity = $newQuantity;
            
            // Jika ada tanggal pembelian baru, simpan sebagai catatan
            $purchaseInfo = '';
            if (!empty($validated['purchase_date'])) {
                $purchaseInfo = " (Tanggal: " . $validated['purchase_date'] . ")";
            }
            
            // Jika ada harga pembelian baru, simpan sebagai catatan
            if (!empty($validated['purchase_price'])) {
                $purchaseInfo .= " (Harga: Rp" . number_format($validated['purchase_price'], 0, ',', '.') . ")";
            }
            
            // Siapkan catatan baru
            $newNotes = "Penambahan stok " . $validated['quantity_to_add'] . " unit pada " . 
                    date('Y-m-d H:i:s') . $purchaseInfo . "\n" . 
                    "Kondisi: " . $validated['condition'];
                    
            // Tambahkan catatan tambahan jika ada
            if (!empty($validated['notes'])) {
                $newNotes .= "\nCatatan: " . $validated['notes'];
            }
            
            // Update notes dengan catatan baru
            $item->notes = $item->notes 
                ? $item->notes . "\n\n" . $newNotes 
                : $newNotes;
            
            // Perbarui status jika barang sebelumnya tidak tersedia karena stok 0
            if ($oldQuantity == 0 && $item->status !== Item::STATUS_BORROWED) {
                if ($validated['condition'] === 'Baik') {
                    $item->status = Item::STATUS_AVAILABLE;
                } else {
                    $item->condition = $validated['condition'];
                    $item->updateStatusFromCondition();
                }
            }
            
            $item->save();
            
            // Generate kode unit yang baru ditambahkan
            $unitCodes = $item->generateUnitCodes();
            $newUnitCodes = array_slice($unitCodes, $oldQuantity, $validated['quantity_to_add']);
            
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