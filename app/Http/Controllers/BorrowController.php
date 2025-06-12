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
        $items = Item::where('status', 'Tersedia')
            ->orderBy('name')
            ->get();

        return view('borrows.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id', function ($attribute, $value, $fail) {
                $item = Item::find($value);
                if ($item && $item->status !== 'Tersedia') {
                    $fail('Barang ini tidak tersedia untuk dipinjam.');
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

            $item->update(['status' => 'Dipinjam']);

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
            $borrowStatus = 'returned'; // Default status
            $itemStatus = 'Tersedia'; // Default status

            if ($validated['action'] === 'lost') {
                $borrowStatus = 'lost';
                $itemStatus = 'Hilang';
                $item->update(['status' => $itemStatus]);

            } else { // action is 'returned'
                $borrow->condition_on_return = $validated['condition_on_return'];
                
                $item->update([
                    'condition' => $validated['condition_on_return']
                ]);

                if ($validated['condition_on_return'] !== 'Baik') {
                    $itemStatus = 'Rusak';
                }
                $item->update(['status' => $itemStatus]);
            }
            
            $borrow->update([
                'status' => $borrowStatus,
                'return_date' => Carbon::now()
            ]);

            DB::commit();

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Status peminjaman berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui status.');
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