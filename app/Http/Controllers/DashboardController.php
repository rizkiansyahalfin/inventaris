<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('petugas')) {
            return $this->staffDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    private function adminDashboard()
    {
        // Statistik untuk admin
        $stats = [
            'total_items' => Item::count(),
            'available_items' => Item::where('status', 'Tersedia')->count(),
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_categories' => Category::count(),
            'total_borrows' => Borrow::count(),
            'pending_requests' => Borrow::where('status', 'pending')->count(),
            'total_notifications' => Notification::count(),
        ];

        // Aktivitas terbaru
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Peminjaman yang perlu persetujuan
        $pendingApprovals = Borrow::with(['user', 'item'])
            ->where('status', 'borrowed')
            ->latest()
            ->take(5)
            ->get();

        // Stok barang yang kritis (kurang dari 5)
        $lowStockItems = Item::with('categories')
            ->where('quantity', '<', 5)
            ->take(5)
            ->get();

        // Data untuk grafik
        $borrowsPerMonth = $this->getBorrowsPerMonth();
        $itemsByCategory = $this->getItemsByCategory();

        // Debug data
        \Log::info('Dashboard Data:', [
            'stats' => $stats,
            'pendingApprovals' => $pendingApprovals->toArray(),
            'lowStockItems' => $lowStockItems->toArray(),
            'borrowsPerMonth' => $borrowsPerMonth,
            'itemsByCategory' => $itemsByCategory
        ]);

        return view('dashboard.admin', compact(
            'stats',
            'recentActivities',
            'pendingApprovals',
            'lowStockItems',
            'borrowsPerMonth',
            'itemsByCategory'
        ));
    }

    private function staffDashboard()
    {
        // Statistik untuk petugas
        $stats = [
            'today_borrows' => Borrow::whereDate('created_at', Carbon::today())->count(),
            'today_returns' => Borrow::whereDate('return_date', Carbon::today())->count(),
            'pending_returns' => Borrow::where('status', 'borrowed')
                ->where('due_date', '<', Carbon::now())
                ->count(),
            'low_stock_items' => Item::where('quantity', '<', 5)->count(),
        ];

        // Peminjaman hari ini
        $todayBorrows = Borrow::with(['user', 'item'])
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->take(5)
            ->get();

        // Pengembalian hari ini
        $todayReturns = Borrow::with(['user', 'item'])
            ->whereDate('return_date', Carbon::today())
            ->latest()
            ->take(5)
            ->get();

        // Barang yang perlu dikembalikan
        $upcomingReturns = Borrow::with(['user', 'item'])
            ->where('status', 'borrowed')
            ->where('due_date', '>', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->get();

        return view('dashboard.staff', compact(
            'stats',
            'todayBorrows',
            'todayReturns',
            'upcomingReturns'
        ));
    }

    private function userDashboard()
    {
        $user = auth()->user();
        
        // Statistik untuk user
        $stats = [
            'active_borrows' => Borrow::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->count(),
            'total_borrows' => Borrow::where('user_id', $user->id)->count(),
            'pending_requests' => Borrow::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
        ];

        // Peminjaman aktif user
        $activeBorrows = Borrow::with('item')
            ->where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->get();

        // Riwayat peminjaman
        $borrowHistory = Borrow::with('item')
            ->where('user_id', $user->id)
            ->where('status', '!=', 'borrowed')
            ->latest()
            ->take(5)
            ->get();

        // Notifikasi user
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.user', compact(
            'stats',
            'activeBorrows',
            'borrowHistory',
            'notifications'
        ));
    }

    private function getBorrowsPerMonth()
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $borrows = Borrow::selectRaw('COUNT(*) as total, MONTH(created_at) as month')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = [];
        foreach ($months as $num => $name) {
            $data[$name] = $borrows->firstWhere('month', $num)?->total ?? 0;
        }

        return $data;
    }

    private function getItemsByCategory()
    {
        $categories = Category::withCount('items')
            ->get();

        $data = [];
        foreach ($categories as $category) {
            $data[$category->name] = $category->items_count;
        }

        return $data;
    }
} 