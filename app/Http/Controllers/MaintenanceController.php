<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maintenances = Maintenance::with(['item', 'user'])->orderBy('start_date', 'desc')->paginate(10);
        return view('maintenances.index', compact('maintenances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Tampilkan semua item yang tidak hilang dan tidak rusak berat
        $items = Item::where('status', '!=', Item::STATUS_LOST)
                    ->orderBy('name')
                    ->get();
                    
        $itemsWithCondition = $items->keyBy('id')->map(function ($item) {
            return ['condition' => $item->condition, 'status' => $item->status];
        });
        
        $selectedItem = $request->get('item_id');
        return view('maintenances.create', compact('items', 'selectedItem', 'itemsWithCondition'));
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

            DB::commit();

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
        return view('maintenances.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maintenance $maintenance)
    {
        //
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
        $maintenance->delete();
        return redirect()->route('maintenances.index')->with('success', 'Data pemeliharaan berhasil dihapus.');
    }
}
