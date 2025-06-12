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
        $items = Item::orderBy('name')->get();
        $itemsWithCondition = $items->keyBy('id')->map(function ($item) {
            return ['condition' => $item->condition];
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
            'update_item_status' => 'nullable|string|in:Perlu Servis,Rusak,Perlu Ganti,Tersedia',
        ]);

        try {
            DB::beginTransaction();

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

            if (isset($validated['update_item_status'])) {
                $item = Item::find($validated['item_id']);
                $item->update(['status' => $validated['update_item_status']]);
            }

            DB::commit();

            return redirect()->route('maintenances.show', $maintenance)->with('success', 'Data pemeliharaan berhasil ditambahkan.');

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
        //
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
