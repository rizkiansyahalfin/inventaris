<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('items')
            ->orderBy('name')
            ->paginate(10);

        // Log activity
        \App\Models\ActivityLog::log('view', 'category', 'Lihat daftar kategori (' . $categories->total() . ' kategori)');

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        // Log activity
        \App\Models\ActivityLog::log('view', 'category', 'Akses halaman tambah kategori baru');
        
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);
        
        // Log activity
        \App\Models\ActivityLog::log('create', 'category', 'Menambah kategori baru: ' . $category->name);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit(Category $category)
    {
        // Log activity
        \App\Models\ActivityLog::log('view', 'category', 'Akses halaman edit kategori: ' . $category->name . ' (Kode: ' . $category->code . ')');
        
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);
        
        // Log activity
        \App\Models\ActivityLog::log('update', 'category', 'Mengedit kategori: ' . $category->name);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(Category $category)
    {
        if ($category->items()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus kategori yang masih memiliki barang');
        }

        $categoryName = $category->name;
        $categoryCode = $category->code;
        $category->delete();
        
        // Log activity
        \App\Models\ActivityLog::log('delete', 'category', 'Menghapus kategori: ' . $categoryName . ' (Kode: ' . $categoryCode . ')');

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
} 