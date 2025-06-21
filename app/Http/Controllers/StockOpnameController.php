<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stockOpnames = StockOpname::with(['creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'stock_opname', 'Lihat daftar stock opname (' . $stockOpnames->total() . ' stock opname)');
        
        return view('stock-opnames.index', compact('stockOpnames'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Log activity
        \App\Models\ActivityLog::log('view', 'stock_opname', 'Akses halaman buat stock opname baru');
        
        return view('stock-opnames.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);
        
        $stockOpname = StockOpname::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);
        
        \App\Models\ActivityLog::log('create', 'stock_opname', 'Menambah stock opname: ' . $stockOpname->id);
        
        return redirect()->route('stock-opnames.show', $stockOpname)
            ->with('success', 'Stock opname berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['items.item', 'items.checkedBy']);
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'stock_opname', 'Lihat detail stock opname: ' . $stockOpname->name . ' (ID: ' . $stockOpname->id . ')');
        
        return view('stock-opnames.show', compact('stockOpname'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending') {
            return back()->with('error', 'Stock opname yang sudah dimulai tidak dapat diubah.');
        }
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'stock_opname', 'Akses halaman edit stock opname: ' . $stockOpname->name . ' (ID: ' . $stockOpname->id . ')');
        
        return view('stock-opnames.edit', compact('stockOpname'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending') {
            return back()->with('error', 'Stock opname yang sudah dimulai tidak dapat diubah.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);
        
        $stockOpname->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
        ]);
        
        \App\Models\ActivityLog::log('update', 'stock_opname', 'Mengedit stock opname: ' . $stockOpname->id);
        
        return redirect()->route('stock-opnames.show', $stockOpname)
            ->with('success', 'Stock opname berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending') {
            return back()->with('error', 'Stock opname yang sudah dimulai tidak dapat dihapus.');
        }
        
        $stockOpname->delete();
        
        \App\Models\ActivityLog::log('delete', 'stock_opname', 'Menghapus stock opname: ' . $stockOpname->id);
        
        return redirect()->route('stock-opnames.index')
            ->with('success', 'Stock opname berhasil dihapus.');
    }
    
    /**
     * Start the stock opname process
     */
    public function start(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending') {
            return back()->with('error', 'Stock opname sudah dimulai atau selesai.');
        }
        
        DB::beginTransaction();
        
        try {
            // Update status
            $stockOpname->update(['status' => 'in_progress']);
            
            // Ambil semua item
            $items = Item::all();
            
            // Buat record stock opname item
            foreach ($items as $item) {
                StockOpnameItem::create([
                    'stock_opname_id' => $stockOpname->id,
                    'item_id' => $item->id,
                    'expected_quantity' => $item->stock,
                    'actual_quantity' => 0, // Akan diisi nanti saat pengecekan
                    'notes' => '',
                ]);
            }
            
            DB::commit();
            
            // Log activity
            \App\Models\ActivityLog::log('start', 'stock_opname', 'Memulai stock opname: ' . $stockOpname->name . ' (ID: ' . $stockOpname->id . ')');
            
            return redirect()->route('stock-opnames.items.index', $stockOpname)
                ->with('success', 'Stock opname telah dimulai. Silakan lakukan pengecekan item.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Display all items for checking
     */
    public function itemsIndex(StockOpname $stockOpname)
    {
        if ($stockOpname->status === 'pending') {
            return redirect()->route('stock-opnames.show', $stockOpname)
                ->with('error', 'Stock opname belum dimulai.');
        }
        
        $items = $stockOpname->items()
            ->with('item')
            ->orderBy('checked_at')
            ->paginate(15);
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'stock_opname', 'Lihat daftar item stock opname: ' . $stockOpname->name . ' (ID: ' . $stockOpname->id . ')');
            
        return view('stock-opnames.items.index', compact('stockOpname', 'items'));
    }
    
    /**
     * Show form to check an item
     */
    public function checkItem(StockOpname $stockOpname, StockOpnameItem $item)
    {
        if ($stockOpname->status !== 'in_progress') {
            return back()->with('error', 'Stock opname tidak dalam proses.');
        }
        
        return view('stock-opnames.items.check', compact('stockOpname', 'item'));
    }
    
    /**
     * Save item check result
     */
    public function saveItemCheck(Request $request, StockOpname $stockOpname, StockOpnameItem $item)
    {
        if ($stockOpname->status !== 'in_progress') {
            return back()->with('error', 'Stock opname tidak dalam proses.');
        }
        
        $validated = $request->validate([
            'actual_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $stockOpnameItem = StockOpnameItem::where('stock_opname_id', $stockOpname->id)
            ->where('item_id', $item->id)
            ->firstOrFail();

        $stockOpnameItem->update([
            'actual_quantity' => $request->actual_quantity,
            'notes' => $request->notes,
        ]);
        
        // Jika ada perbedaan, perbarui stock di item
        if ($request->actual_quantity != $item->stock) {
            $item->item->update(['stock' => $request->actual_quantity]);
        }
        
        return redirect()->route('stock-opnames.items.index', $stockOpname)
            ->with('success', 'Item berhasil dicek.');
    }
    
    /**
     * Complete the stock opname
     */
    public function complete(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'in_progress') {
            return back()->with('error', 'Stock opname tidak dalam proses.');
        }
        
        if ($stockOpname->items()->whereNull('checked_at')->exists()) {
            return back()->with('error', 'Semua item harus dicek terlebih dahulu.');
        }
        
        $stockOpname->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Log activity
        \App\Models\ActivityLog::log('complete', 'stock_opname', 'Menyelesaikan stock opname: ' . $stockOpname->name . ' (ID: ' . $stockOpname->id . ')');
        
        return redirect()->route('stock-opnames.show', $stockOpname)
            ->with('success', 'Stock opname berhasil diselesaikan.');
    }
}
