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
        $items = Item::where('status', Item::STATUS_AVAILABLE)
            ->where('condition', '!=', 'Rusak Berat')
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();

        return view('borrows.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id', function ($attribute, $value, $fail) {
                $item = Item::find($value);
                if (!$item) return;
                if ($item->status !== Item::STATUS_AVAILABLE) {
                    $fail('Barang ini tidak tersedia untuk dipinjam.');
                }
                if ($item->condition === 'Rusak Berat') {
                    $fail('Barang dengan kondisi "Rusak Berat" tidak dapat dipinjam.');
                }
                if ($item->quantity < 1) {
                    $fail('Stok barang tidak mencukupi.');
                }
            }],
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::findOrFail($validated['item_id']);

            $borrow = Borrow::create([
                'user_id' => auth()->id(),
                'item_id' => $validated['item_id'],
                'borrow_date' => $validated['borrow_date'],
                'due_date' => $validated['due_date'],
                'status' => 'borrowed',
                'notes' => $validated['notes'],
                'condition_at_borrow' => $item->condition,
            ]);

            // Kurangi jumlah dan perbarui status
            $item->decrement('quantity');
            
            // Jika jumlah menjadi 0, update status menjadi 'Dipinjam'
            if ($item->quantity === 0) {
                $item->updateStatus(Item::STATUS_BORROWED);
            }

            DB::commit();

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Peminjaman berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Borrow $borrow)
    {
        $borrow->load(['user', 'item', 'attachments']);
        return view('borrows.show', compact('borrow'));
    }

    public function updateStatus(Request $request, Borrow $borrow)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:returned,lost',
            'condition_on_return' => 'required_if:action,returned|string|in:Baik,Rusak Ringan,Rusak Berat'
        ]);

        if (in_array($borrow->status, ['returned', 'lost'])) {
            return back()->with('error', 'Status peminjaman ini sudah final.');
        }

        try {
            DB::beginTransaction();

            $item = $borrow->item;
            
            if ($validated['action'] === 'lost') {
                // Update status peminjaman menjadi hilang
                $borrow->update([
                    'status' => 'lost',
                    'return_date' => Carbon::now()
                ]);
                
                // Update status item menjadi hilang jika semua stok hilang
                if ($item->quantity == 0) {
                    $item->updateStatus(Item::STATUS_LOST);
                }
            } else {
                // Kembalikan barang
                $borrow->update([
                    'status' => 'returned',
                    'return_date' => Carbon::now(),
                    'condition_on_return' => $validated['condition_on_return']
                ]);
                
                // Tambah jumlah karena barang dikembalikan
                $item->increment('quantity');
                
                // Perbarui kondisi barang berdasarkan kondisi saat dikembalikan
                if ($validated['condition_on_return'] !== $item->condition) {
                    $item->updateCondition($validated['condition_on_return']);
                } else {
                    // Jika kondisi sama tapi barang baru dikembalikan,
                    // perbarui status berdasarkan kondisi
                    $item->updateStatusFromCondition();
                    $item->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Status peminjaman berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui status: ' . $e->getMessage());
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