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
        try {
            // Barang dengan stok rendah
            $lowStockItems = Item::with('categories')
                ->where('stock', '<', 5)
                ->limit(5)
                ->get();

            // Statistik untuk dashboard
            $stats = [
                'total_items' => Item::count(),
                'total_borrows' => Borrow::count(),
                'total_users' => User::count(),
                'pending_requests' => Borrow::where('approval_status', 'pending')->count(),
                'low_stock_items' => Item::where('stock', '<', 5)->count(),
            ];

            // Aktivitas terbaru
            $recentActivities = ActivityLog::with('user')
                ->latest()
                ->take(10)
                ->get();

            // Peminjaman yang perlu persetujuan
            $pendingApprovals = Borrow::with(['user', 'item'])
                ->where('approval_status', 'pending')
                ->latest()
                ->take(5)
                ->get();

            // Data untuk grafik
            $borrowsPerMonth = $this->getBorrowsPerMonth();
            $itemsByCategory = $this->getItemsByCategory();

            return view('dashboard.admin', compact(
                'stats',
                'recentActivities',
                'pendingApprovals',
                'lowStockItems',
                'borrowsPerMonth',
                'itemsByCategory'
            ));
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            
            // Return default values if there's an error
            return view('dashboard.admin', [
                'stats' => [
                    'total_items' => 0,
                    'total_borrows' => 0,
                    'total_users' => 0,
                    'pending_requests' => 0,
                    'low_stock_items' => 0,
                ],
                'recentActivities' => collect(),
                'pendingApprovals' => collect(),
                'lowStockItems' => collect(),
                'borrowsPerMonth' => [],
                'itemsByCategory' => [],
            ]);
        }
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
            'low_stock_items' => Item::where('stock', '<', 5)->count(),
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