<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog;
use App\Models\ItemRequest;
use App\Models\Notification;
use App\Models\User;
use App\Http\Requests\StoreItemRequestRequest;
use App\Http\Requests\UpdateItemRequestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ItemRequest::with(['user', 'item']);

        // Jika user biasa, hanya tampilkan permintaan miliknya
        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        }

        // Filter berdasarkan request
        $query->when($request->status, function ($query, $status) {
            return $query->where('status', $status);
        })
            ->when($request->search, function ($query, $search) {
                return $query->where('item_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });

        $itemRequests = $query->orderBy('created_at', 'desc')->paginate(10);

        // Log activity
        $filters = [];
        if ($request->status)
            $filters[] = 'status: ' . $request->status;
        if ($request->search)
            $filters[] = 'pencarian: ' . $request->search;

        $filterDescription = !empty($filters) ? 'Lihat daftar permintaan barang dengan filter: ' . implode(', ', $filters) : 'Lihat daftar permintaan barang';
        ActivityLog::log('view', 'item_request', $filterDescription . ' (' . $itemRequests->total() . ' permintaan)');

        return view('item-requests.index', compact('itemRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Log activity
        ActivityLog::log('view', 'item_request', 'Akses halaman buat permintaan barang baru');
        $categories = Category::orderBy('name')->get();
        return view('item-requests.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequestRequest $request)
    {

        $itemRequest = ItemRequest::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Notifikasi admin dan petugas
        $officers = User::whereIn('role', ['admin', 'petugas'])->get();
        foreach ($officers as $officer) {
            Notification::create([
                'user_id' => $officer->id,
                'type' => 'item_request',
                'title' => 'Permintaan Item Baru',
                'message' => "User " . Auth::user()->name . " mengajukan permintaan item baru: {$request->name}",
                'data' => json_encode(['item_request_id' => $itemRequest->id]),
            ]);
        }

        ActivityLog::log('create', 'item_request', 'Menambah permintaan barang: ' . $itemRequest->id);

        return redirect()->route('item-requests.show', $itemRequest)
            ->with('success', 'Permintaan item berhasil diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemRequest $itemRequest)
    {
        $user = Auth::user();

        // Pastikan hanya user pemilik, admin, atau petugas yang dapat melihat permintaan
        if (!$user->isAdmin() && !$user->isPetugas() && $itemRequest->user_id !== $user->id) {
            abort(403);
        }

        // Log activity
        ActivityLog::log('view', 'item_request', 'Lihat detail permintaan barang: ' . $itemRequest->name . ' (ID: ' . $itemRequest->id . ')');

        return view('item-requests.show', compact('itemRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ItemRequest $itemRequest)
    {
        // Pastikan hanya user pemilik yang dapat edit permintaan yang masih pending
        if ($itemRequest->user_id !== Auth::id() || !$itemRequest->isPending()) {
            abort(403);
        }

        $categories = Category::orderBy('name')->get();

        // Log activity
        ActivityLog::log('view', 'item_request', 'Akses halaman edit permintaan barang: ' . $itemRequest->name . ' (ID: ' . $itemRequest->id . ')');

        return view('item-requests.edit', compact('itemRequest', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequestRequest $request, ItemRequest $itemRequest)
    {
        // Authorization handled by UpdateItemRequestRequest

        $itemRequest->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'quantity' => $request->quantity,
            'reason' => $request->reason,
        ]);

        ActivityLog::log('update', 'item_request', 'Mengedit permintaan barang: ' . $itemRequest->id);

        return redirect()->route('item-requests.show', $itemRequest)
            ->with('success', 'Permintaan item berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemRequest $itemRequest)
    {
        // Pastikan hanya user pemilik yang dapat menghapus permintaan yang masih pending
        // atau admin dapat menghapus permintaan
        $user = Auth::user();
        if (
            (!$user->isAdmin() && $itemRequest->user_id !== $user->id) ||
            (!$user->isAdmin() && !$itemRequest->isPending())
        ) {
            abort(403);
        }

        $itemRequest->delete();

        ActivityLog::log('delete', 'item_request', 'Menghapus permintaan barang: ' . $itemRequest->id);

        return redirect()->route('item-requests.index')
            ->with('success', 'Permintaan item berhasil dihapus.');
    }

    /**
     * Update the status of the request
     */
    public function updateStatus(Request $request, ItemRequest $itemRequest)
    {
        // Pastikan hanya admin/petugas yang dapat memperbarui status
        if (!Auth::user()->isAdmin() && !Auth::user()->isPetugas()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,completed',
            'review_notes' => 'nullable|string',
        ]);

        $itemRequest->update([
            'status' => $request->status,
            'review_notes' => $request->review_notes,
            'reviewed_by' => Auth::id(),
        ]);

        // Notifikasi ke pemohon
        Notification::create([
            'user_id' => $itemRequest->user_id,
            'type' => 'item_request_' . $request->status,
            'title' => 'Status Permintaan Item Diperbarui',
            'message' => 'Permintaan item ' . $itemRequest->name . ' telah ' .
                ($request->status === 'approved' ? 'disetujui' :
                    ($request->status === 'rejected' ? 'ditolak' : 'selesai')) . '.',
            'data' => json_encode(['item_request_id' => $itemRequest->id]),
        ]);

        // Log activity
        ActivityLog::log('update_status', 'item_request', 'Update status permintaan barang: ' . $itemRequest->name . ' menjadi ' . $request->status . ' (ID: ' . $itemRequest->id . ')');

        return redirect()->route('item-requests.show', $itemRequest)
            ->with('success', 'Status permintaan berhasil diperbarui.');
    }
}
