<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\ActivityLog;
use App\Services\ItemService;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Requests\UpdateStockRequest;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(
        private ItemService $itemService
    ) {
    }

    public function index(Request $request)
    {
        $query = Item::with(['category', 'location'])
            ->when($request->search, fn($query, $search) => $query->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
            ->when($request->category_id, fn($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($request->status, fn($query, $status) => $query->where('status', $status));

        $items = $query->orderBy('name')->paginate(10);
        $categories = Category::orderBy('name')->get();
        $statuses = Item::getStatuses();

        $filters = [];
        if ($request->search)
            $filters[] = 'pencarian: ' . $request->search;
        if ($request->category_id)
            $filters[] = 'kategori: ' . Category::find($request->category_id)->name;
        if ($request->status)
            $filters[] = 'status: ' . $request->status;

        $filterDescription = !empty($filters) ? 'Lihat daftar barang dengan filter: ' . implode(', ', $filters) : 'Lihat daftar barang';
        ActivityLog::log('view', 'item', $filterDescription . ' (' . $items->total() . ' item)');

        return view('items.index', compact('items', 'categories', 'statuses'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $statuses = Item::getStatuses();

        ActivityLog::log('view', 'item', 'Akses halaman tambah barang baru');

        return view('items.create', compact('categories', 'locations', 'statuses'));
    }

    public function store(StoreItemRequest $request)
    {
        $result = $this->itemService->createItem($request->validated(), $request->file('image'));

        $item = $result['item'];
        $unitCodes = $result['unitCodes'];

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
        $item->load(['category', 'location', 'attachments', 'borrows.user'])->loadCount('borrows');

        $baseCode = preg_replace('/-\d+$/', '', $item->code);
        $relatedItems = Item::where(function ($query) use ($baseCode) {
            $query->where('code', 'like', $baseCode . '-%')
                ->orWhere('code', $baseCode);
        })
            ->orderBy('code')
            ->get();

        $unitCodes = $relatedItems->pluck('code')->toArray();

        ActivityLog::log('view', 'item', 'Lihat detail barang: ' . $item->name . ' (Kode: ' . $item->code . ')');

        return view('items.show', compact('item', 'unitCodes', 'relatedItems'));
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $statuses = Item::getStatuses();

        ActivityLog::log('view', 'item', 'Akses halaman edit barang: ' . $item->name . ' (Kode: ' . $item->code . ')');

        return view('items.edit', compact('item', 'categories', 'locations', 'statuses'));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $result = $this->itemService->updateItem($item, $request->validated(), $request->file('image'));

        $message = 'Barang berhasil diperbarui';
        if ($request->validated()['stock'] !== $item->stock || $result['regenerateCode']) {
            $message .= ' dengan perubahan kode unit';
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
        ActivityLog::log('delete', 'barang', 'Menghapus barang: ' . $item->name . ' (ID: ' . $item->id . ')');

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus');
    }

    public function showAddStockForm(Item $item)
    {
        ActivityLog::log('view', 'item', 'Akses halaman tambah stok barang: ' . $item->name . ' (Kode: ' . $item->code . ')');

        return view('items.add-stock', compact('item'));
    }

    public function addStock(UpdateStockRequest $request, Item $item)
    {
        try {
            $newUnitCodes = $this->itemService->addStock($item, $request->validated());

            $message = 'Penambahan stok berhasil sebanyak ' . $request->validated()['quantity_to_add'] . ' unit';
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
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambah stok: ' . $e->getMessage());
        }
    }
}