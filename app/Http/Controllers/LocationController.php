<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::withCount('items')->orderBy('name')->paginate(10);

        // Log activity
        \App\Models\ActivityLog::log('view', 'location', 'Lihat daftar lokasi (' . $locations->total() . ' lokasi)');

        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Log activity
        \App\Models\ActivityLog::log('view', 'location', 'Akses halaman tambah lokasi baru');

        return view('locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'description' => 'nullable|string',
        ]);

        $location = Location::create($validated);

        // Log activity
        \App\Models\ActivityLog::log('create', 'location', 'Menambah lokasi baru: ' . $location->name);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location->load(['items.category']);

        // Log activity
        \App\Models\ActivityLog::log('view', 'location', 'Lihat detail lokasi: ' . $location->name);

        return view('locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        // Log activity
        \App\Models\ActivityLog::log('view', 'location', 'Akses halaman edit lokasi: ' . $location->name);

        return view('locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'description' => 'nullable|string',
        ]);

        $location->update($validated);

        // Log activity
        \App\Models\ActivityLog::log('update', 'location', 'Mengedit lokasi: ' . $location->name);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Lokasi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        if ($location->items()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus lokasi yang masih memiliki barang');
        }

        $locationName = $location->name;
        $location->delete();

        // Log activity
        \App\Models\ActivityLog::log('delete', 'location', 'Menghapus lokasi: ' . $locationName);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Lokasi berhasil dihapus');
    }
}
