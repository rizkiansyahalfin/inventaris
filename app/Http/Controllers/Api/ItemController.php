<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Item::with(['categories', 'attachments'])
            ->orderBy('name')
            ->paginate(10);

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:items,code',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string',
            'location' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'category_ids' => 'array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::create($validated);

            if (!empty($validated['category_ids'])) {
                $item->categories()->attach($validated['category_ids']);
            }

            DB::commit();

            return response()->json($item->load('categories'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Item $item): JsonResponse
    {
        return response()->json(
            $item->load(['categories', 'attachments', 'borrows.user'])
        );
    }

    public function update(Request $request, Item $item): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:items,code,' . $item->id,
            'description' => 'nullable|string',
            'stock' => 'sometimes|integer|min:0',
            'condition' => 'sometimes|string',
            'location' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
        ]);

        $item->update($validated);

        return response()->json($item->load('categories'));
    }

    public function destroy(Item $item): JsonResponse
    {
        if ($item->borrows()->where('status', 'borrowed')->exists()) {
            throw ValidationException::withMessages([
                'item' => ['Cannot delete item while it is being borrowed'],
            ]);
        }

        $item->delete();

        return response()->json(null, 204);
    }

    public function attachCategory(Item $item, Category $category): JsonResponse
    {
        $item->categories()->attach($category->id);

        return response()->json($item->load('categories'));
    }

    public function detachCategory(Item $item, Category $category): JsonResponse
    {
        $item->categories()->detach($category->id);

        return response()->json($item->load('categories'));
    }
} 