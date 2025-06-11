<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrow::with(['user', 'item'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('item', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });

        $borrows = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('borrows.index', compact('borrows'));
    }

    public function create()
    {
        $items = Item::whereDoesntHave('borrows', function ($query) {
                $query->where('status', 'borrowed');
            })
            ->orderBy('name')
            ->get();

        return view('borrows.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'notes' => 'nullable|string',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        if ($item->borrows()->where('status', 'borrowed')->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Barang ini sedang dipinjam.');
        }

        $borrow = Borrow::create([
            'user_id' => auth()->id(),
            'item_id' => $validated['item_id'],
            'borrow_date' => $validated['borrow_date'],
            'due_date' => $validated['due_date'],
            'status' => 'borrowed',
            'notes' => $validated['notes'],
        ]);

        return redirect()
            ->route('borrows.show', $borrow)
            ->with('success', 'Peminjaman berhasil dibuat');
    }

    public function show(Borrow $borrow)
    {
        $borrow->load(['user', 'item', 'attachments']);
        return view('borrows.show', compact('borrow'));
    }

    public function return(Borrow $borrow)
    {
        if ($borrow->status === 'returned') {
            return back()->with('error', 'Peminjaman ini sudah dikembalikan');
        }

        try {
            DB::beginTransaction();

            $borrow->update([
                'return_date' => Carbon::now(),
                'status' => 'returned',
            ]);

            DB::commit();

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Barang berhasil dikembalikan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengembalikan barang');
        }
    }

    public function destroy(Borrow $borrow)
    {
        if ($borrow->status === 'borrowed') {
            return back()->with('error', 'Tidak dapat menghapus data peminjaman yang masih aktif');
        }

        $borrow->delete();

        return redirect()
            ->route('borrows.index')
            ->with('success', 'Data peminjaman berhasil dihapus');
    }
} 