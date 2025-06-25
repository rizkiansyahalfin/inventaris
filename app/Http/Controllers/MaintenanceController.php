<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Maintenance::with(['item', 'user']);

        // Filter berdasarkan request
        $query->when($request->status, function ($query, $status) {
                if ($status === 'completed') {
                    return $query->whereNotNull('completion_date');
                } elseif ($status === 'ongoing') {
                    return $query->whereNull('completion_date');
                }
                return $query;
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('item', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                               ->orWhere('code', 'like', "%{$search}%");
                  });
            })
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            });

        $maintenances = $query->orderBy('created_at', 'desc')->paginate(10);

        // Log activity
        $filters = [];
        if ($request->status) $filters[] = 'status: ' . $request->status;
        if ($request->search) $filters[] = 'pencarian: ' . $request->search;
        if ($request->type) $filters[] = 'tipe: ' . $request->type;
        
        $filterDescription = !empty($filters) ? 'Lihat daftar maintenance dengan filter: ' . implode(', ', $filters) : 'Lihat daftar maintenance';
        \App\Models\ActivityLog::log('view', 'maintenance', $filterDescription . ' (' . $maintenances->total() . ' maintenance)');

        return view('maintenances.index', compact('maintenances'));
    }

    /**
     * Export maintenance data to PDF (public route)
     */
    public function exportPdf(Request $request)
    {
        $query = Maintenance::with(['item', 'user']);

        // Terapkan filter yang sama seperti index
        $query->when($request->status, function ($query, $status) {
                if ($status === 'completed') {
                    return $query->whereNotNull('completion_date');
                } elseif ($status === 'ongoing') {
                    return $query->whereNull('completion_date');
                }
                return $query;
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('item', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            });

        $maintenances = $query->orderBy('created_at', 'desc')->get();

        // Log activity
        $filters = [];
        if ($request->status) $filters[] = 'status: ' . $request->status;
        if ($request->search) $filters[] = 'pencarian: ' . $request->search;
        if ($request->type) $filters[] = 'tipe: ' . $request->type;
        $filterDescription = !empty($filters) ? 'Export PDF maintenance dengan filter: ' . implode(', ', $filters) : 'Export PDF semua maintenance';
        \App\Models\ActivityLog::log('export', 'maintenance', $filterDescription . ' (' . $maintenances->count() . ' maintenance)');

        // Generate PDF
        if (class_exists('\PDF')) {
            $pdf = \PDF::loadView('maintenances.pdf', compact('maintenances'));
            return $pdf->download('riwayat-pemeliharaan-' . date('Y-m-d') . '.pdf');
        } else {
            return redirect()->route('maintenances.index')
                ->with('error', 'Export PDF tidak tersedia. Silakan install package PDF terlebih dahulu.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::where('status', Item::STATUS_MAINTENANCE)
            ->orWhere('condition', '!=', 'Baik')
                    ->orderBy('name')
                    ->get();
                    
        // Prepare items data for JavaScript
        $itemsWithCondition = $items->keyBy('id')->map(function ($item) {
            return ['condition' => $item->condition];
        });

        // Get selected item from query parameter
        $selectedItem = request('item_id');

        // Log activity
        \App\Models\ActivityLog::log('view', 'maintenance', 'Akses halaman tambah maintenance baru');

        return view('maintenances.create', compact('items', 'itemsWithCondition', 'selectedItem'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|string|in:Perawatan,Perbaikan,Penggantian',
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'completion_date' => 'nullable|date|after_or_equal:start_date',
            'update_condition' => 'nullable|string|in:Baik,Rusak Ringan,Rusak Berat',
            'update_item_status' => 'nullable|string|in:Tersedia,Perlu Servis,Rusak,Perlu Ganti',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::findOrFail($validated['item_id']);
            
            // Buat catatan perawatan
            $maintenance = Maintenance::create([
                'item_id' => $validated['item_id'],
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'title' => $validated['title'],
                'notes' => $validated['notes'],
                'cost' => $validated['cost'],
                'start_date' => $validated['start_date'],
                'completion_date' => $validated['completion_date'],
            ]);

            // Jika belum selesai, update status menjadi dalam perawatan
            if (empty($validated['completion_date'])) {
                // Hanya update jika item tidak sedang dipinjam
                if ($item->status !== Item::STATUS_BORROWED) {
                    $item->updateStatus(Item::STATUS_MAINTENANCE);
                }
            }
            // Jika sudah selesai dan ada update kondisi
            elseif (isset($validated['update_condition'])) {
                // Update kondisi, yang akan mengupdate status otomatis sesuai kondisi
                $item->updateCondition($validated['update_condition']);
            }
            // Jika ada update status manual
            elseif (isset($validated['update_item_status'])) {
                $item->updateStatus($validated['update_item_status']);
            }

            DB::commit();

            \App\Models\ActivityLog::log('create', 'maintenance', 'Menambah perawatan untuk item: ' . $item->name . ' (Maint. ID: ' . $maintenance->id . ')');

            return redirect()->route('maintenances.show', $maintenance)
                ->with('success', 'Data pemeliharaan berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Maintenance $maintenance)
    {
        $maintenance->load(['item', 'user']);
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'maintenance', 'Lihat detail maintenance: ' . $maintenance->title . ' (ID: ' . $maintenance->id . ')');
        
        return view('maintenances.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maintenance $maintenance)
    {
        $maintenance->load(['item', 'user']);
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'maintenance', 'Akses halaman edit maintenance: ' . $maintenance->title . ' (ID: ' . $maintenance->id . ')');
        
        return view('maintenances.edit', compact('maintenance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'completion_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'update_condition' => 'nullable|string|in:Baik,Rusak Ringan,Rusak Berat',
        ]);
        
        try {
            DB::beginTransaction();
            
            $maintenance->update([
                'completion_date' => $validated['completion_date'] ?? $maintenance->completion_date,
                'notes' => $validated['notes'] ?? $maintenance->notes,
            ]);
            
            // Jika menyelesaikan perawatan dan ada update kondisi
            if (!empty($validated['completion_date']) && isset($validated['update_condition'])) {
                $item = $maintenance->item;
                // Update kondisi, yang akan mengupdate status otomatis
                $item->updateCondition($validated['update_condition']);
            }
            
            DB::commit();

            \App\Models\ActivityLog::log('update', 'maintenance', 'Memperbarui perawatan: ' . $maintenance->title . ' (Maint. ID: ' . $maintenance->id . ')');

            return redirect()->route('maintenances.show', $maintenance)
                ->with('success', 'Data pemeliharaan berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maintenance $maintenance)
    {
        $maintenanceName = $maintenance->title;
        $maintenanceId = $maintenance->id;
        $maintenance->delete();
        \App\Models\ActivityLog::log('delete', 'maintenance', 'Menghapus perawatan: ' . $maintenanceName . ' (Maint. ID: ' . $maintenanceId . ')');
        return redirect()->route('maintenances.index')->with('success', 'Data pemeliharaan berhasil dihapus.');
    }
}
