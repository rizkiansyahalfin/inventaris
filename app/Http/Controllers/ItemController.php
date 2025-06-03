<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

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
            'code' => 'required|string|unique:items,code',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'condition' => 'required|string',
            'location' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $item = Item::create($validated);
        $item->categories()->attach($request->category_ids);

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function show(Item $item)
    {
        $item->load(['categories', 'attachments', 'borrows.user']);
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
            'code' => 'required|string|unique:items,code,' . $item->id,
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'condition' => 'required|string',
            'location' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $item->update($validated);
        $item->categories()->sync($request->category_ids);

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Item $item)
    {
        if ($item->borrows()->where('status', 'borrowed')->exists()) {
            return back()->with('error', 'Tidak dapat menghapus barang yang sedang dipinjam');
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus');
    }
} 