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

        return view('items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'condition' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|string|in:Tersedia,Dipinjam,Hilang,Perlu Servis,Rusak,Perlu Ganti',
            'location' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'required|date',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $item = null;

        DB::transaction(function () use ($validated, $request, &$item) {
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('items', 'public');
                $validated['image'] = basename($path);
            }
    
            // Create item without code first
            $item = Item::create($validated);
            $item->categories()->attach($request->category_ids);

            // Now, generate and save the code
            $item->code = $this->generateItemCode($item);
            $item->save();
        });


        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Barang berhasil ditambahkan dengan kode: ' . $item->code);
    }

    public function show(Item $item)
    {
        $item->load(['categories', 'attachments', 'borrows.user'])->loadCount('borrows');
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'condition' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|string|in:Tersedia,Dipinjam,Hilang,Perlu Servis,Rusak,Perlu Ganti',
            'location' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'required|date',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if code needs regeneration
        $purchaseDateChanged = Carbon::parse($validated['purchase_date'])->notEqualTo($item->purchase_date);
        $categoriesChanged = !empty(array_diff($item->categories->pluck('id')->all(), $validated['category_ids']));
        
        $regenerateCode = $purchaseDateChanged || $categoriesChanged;

        DB::transaction(function() use ($request, $item, $validated, $regenerateCode) {
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
                // Reload the item to get fresh relations
                $item->refresh(); 
                $item->code = $this->generateItemCode($item);
                $item->save();
            }
        });


        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Barang berhasil diperbarui');
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

    private function generateItemCode(Item $item): string
    {
        // 1. Get Category Code
        $primaryCategory = $item->categories()->first();
        if (!$primaryCategory) {
            // Fallback or throw error if no category is assigned
            return "NO-CAT-" . time(); 
        }
        $categoryCode = $primaryCategory->code;

        // 2. Get Purchase Date Code
        $dateCode = $item->purchase_date->format('ym');

        // 3. Find Sequence
        $codePrefix = "{$categoryCode}/{$dateCode}/";
        
        $latestItem = Item::where('code', 'like', $codePrefix . '%')
            ->where('id', '!=', $item->id) // Exclude self
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