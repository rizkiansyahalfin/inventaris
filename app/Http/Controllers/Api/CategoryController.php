<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::with('items')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);
        if (auth()->check()) {
            \App\Models\ActivityLog::log('create', 'kategori_api', 'Menambah kategori (API): ' . $category->name . ' oleh ' . auth()->user()->name);
        }
        return response()->json($category, 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json(
            $category->load('items')
        );
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);
        if (auth()->check()) {
            \App\Models\ActivityLog::log('update', 'kategori_api', 'Mengedit kategori (API): ' . $category->name . ' oleh ' . auth()->user()->name);
        }
        return response()->json($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        // Cek apakah kategori masih memiliki barang
        if ($category->items()->exists()) {
            throw ValidationException::withMessages([
                'category' => ['Tidak dapat menghapus kategori yang masih memiliki barang'],
            ]);
        }

        $category->delete();
        if (auth()->check()) {
            \App\Models\ActivityLog::log('delete', 'kategori_api', 'Menghapus kategori (API): ' . $category->name . ' oleh ' . auth()->user()->name);
        }
        return response()->json(null, 204);
    }
} 