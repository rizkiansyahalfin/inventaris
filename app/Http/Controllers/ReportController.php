<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Borrow;
use App\Exports\ItemsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Excel;

class ReportController extends Controller
{
    /**
     * Konstruktor dengan middleware role
     */
    public function __construct()
    {
        $this->middleware('role:admin,petugas');
    }
    
    public function index(Request $request)
    {
        $query = Item::with('categories');

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
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
        // Khusus admin bisa export
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat melakukan export laporan');
        }
        
        $query = Item::with('categories');

        // Apply filters
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
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

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.pdf', compact('items'));
            return $pdf->download('laporan-barang.pdf');
        }

        if ($format === 'excel') {
            return Excel::download(new ItemsExport($items), 'laporan-barang.xlsx');
        }

        return back()->with('error', 'Format tidak didukung');
    }
}
