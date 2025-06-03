<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Item;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik umum
        $stats = [
            'total_items' => Item::count(),
            'total_categories' => Category::count(),
            'active_borrows' => Borrow::where('status', 'borrowed')->count(),
            'total_borrows' => Borrow::count(),
        ];

        // Barang dengan stok menipis (kurang dari 5)
        $lowStockItems = Item::where('quantity', '<', 5)
            ->with('categories')
            ->get();

        // Peminjaman yang akan jatuh tempo dalam 7 hari
        $upcomingDueDate = Borrow::where('status', 'borrowed')
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->with(['user', 'item'])
            ->get();

        // Peminjaman terlambat
        $overdueItems = Borrow::where('status', 'borrowed')
            ->where('due_date', '<', Carbon::now())
            ->with(['user', 'item'])
            ->get();

        // 5 barang yang paling sering dipinjam
        $mostBorrowedItems = Item::withCount('borrows')
            ->orderBy('borrows_count', 'desc')
            ->limit(5)
            ->get();

        // Data untuk grafik peminjaman per bulan
        $borrowsPerMonth = Borrow::selectRaw('COUNT(*) as total, MONTH(created_at) as month')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        return view('dashboard', compact(
            'stats',
            'lowStockItems',
            'upcomingDueDate',
            'overdueItems',
            'mostBorrowedItems',
            'borrowsPerMonth'
        ));
    }
} 