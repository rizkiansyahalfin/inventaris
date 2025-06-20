<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $borrows = Borrow::with(['user', 'item'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($borrows);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after:borrow_date',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Cek ketersediaan stok
            $item = Item::findOrFail($validated['item_id']);
            
            if ($item->stock < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => ['Stok barang tidak mencukupi'],
                ]);
            }

            // Kurangi stok barang
            $item->decrement('stock', $validated['quantity']);

            // Buat peminjaman
            $borrow = Borrow::create([
                'user_id' => auth()->id(),
                'item_id' => $validated['item_id'],
                'quantity' => $validated['quantity'],
                'borrow_date' => $validated['borrow_date'],
                'due_date' => $validated['due_date'],
                'status' => 'borrowed',
                'notes' => $validated['notes'],
            ]);
            \App\Models\ActivityLog::log('create', 'peminjaman_api', 'Mengajukan peminjaman barang (API): ' . ($item->name ?? '-') . ' (ID: ' . $borrow->id . ')');

            DB::commit();

            return response()->json($borrow->load(['user', 'item']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Borrow $borrow): JsonResponse
    {
        return response()->json(
            $borrow->load(['user', 'item', 'attachments'])
        );
    }

    public function return(Request $request, Borrow $borrow): JsonResponse
    {
        if ($borrow->status === 'returned') {
            throw ValidationException::withMessages([
                'borrow' => ['Peminjaman ini sudah dikembalikan'],
            ]);
        }

        try {
            DB::beginTransaction();

            // Kembalikan stok barang
            $borrow->item->increment('stock', $borrow->quantity);

            // Update status peminjaman
            $borrow->update([
                'return_date' => Carbon::now(),
                'status' => 'returned',
            ]);
            \App\Models\ActivityLog::log('return', 'peminjaman_api', 'Mengembalikan barang (API): ' . ($borrow->item->name ?? '-') . ' (ID: ' . $borrow->id . ')');

            DB::commit();

            return response()->json($borrow->load(['user', 'item']));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Borrow $borrow): JsonResponse
    {
        if ($borrow->status === 'borrowed') {
            throw ValidationException::withMessages([
                'borrow' => ['Tidak dapat menghapus data peminjaman yang masih aktif'],
            ]);
        }

        $borrow->delete();
        \App\Models\ActivityLog::log('delete', 'peminjaman_api', 'Menghapus data peminjaman (API): ID ' . $borrow->id . ' (Barang: ' . ($borrow->item->name ?? '-') . ')');

        return response()->json(null, 204);
    }
} 