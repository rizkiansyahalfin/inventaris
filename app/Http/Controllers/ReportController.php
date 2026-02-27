<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Borrow;
use App\Models\Maintenance;
use App\Exports\ItemsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display the main reports dashboard.
     */
    public function index(Request $request)
    {
        $query = Item::with('category');

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // Get statistics
        $totalItems = Item::count();
        $availableItems = Item::where('status', 'Tersedia')->count();
        $damagedItems = Item::whereIn('status', ['Rusak', 'Hilang'])->count();
        $totalTransactions = Borrow::count();

        // Get category statistics
        $categoryStats = Category::withCount('items')->get();

        // Get status statistics
        $statusStats = Item::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Get all categories for filter
        $categories = Category::all();

        // Get paginated items
        $items = $query->paginate(10);

        // Log activity
        ActivityLog::log('view', 'report', 'Akses halaman laporan (Items: ' . $totalItems . ', Borrows: ' . $totalTransactions . ')');

        return view('reports.index', compact(
            'items',
            'categories',
            'totalItems',
            'availableItems',
            'damagedItems',
            'totalTransactions',
            'categoryStats',
            'statusStats'
        ));
    }

    public function export(Request $request, $format)
    {
        // Pastikan hanya admin yang dapat export
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = Item::with('category');

        // Apply filters
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $items = $query->get();

        // Log activity
        ActivityLog::log('export', 'report', 'Export laporan dalam format: ' . $format);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', compact('items'));
            return $pdf->download('laporan-barang.pdf');
        }

        if ($format === 'excel') {
            return Excel::download(new ItemsExport($items), 'laporan-barang.xlsx');
        }

        return back()->with('error', 'Format tidak didukung');
    }
}
