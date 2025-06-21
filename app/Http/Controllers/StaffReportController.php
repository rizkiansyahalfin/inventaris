<?php

namespace App\Http\Controllers;

use App\Models\StaffReport;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $staffReports = StaffReport::with(['user', 'reviewer'])
                ->latest()
                ->paginate(15);
        } else {
            $staffReports = $user->staffReports()
                ->with('reviewer')
                ->latest()
                ->paginate(15);
        }
        
        // Data untuk dashboard
        if ($user->isAdmin()) {
            $totalReports = StaffReport::count();
            $pendingReviews = StaffReport::where('status', 'submitted')->count();
            $reviewedReports = StaffReport::where('status', 'reviewed')->count();
            $draftReports = StaffReport::where('status', 'draft')->count();
            $totalHours = StaffReport::sum('hours_worked');
            $recentReports = StaffReport::with(['user', 'reviewer'])->latest()->take(5)->get();
        } else {
            $totalReports = $user->staffReports()->count();
            $pendingReviews = $user->staffReports()->where('status', 'submitted')->count();
            $reviewedReports = $user->staffReports()->where('status', 'reviewed')->count();
            $draftReports = $user->staffReports()->where('status', 'draft')->count();
            $totalHours = $user->staffReports()->sum('hours_worked');
            $recentReports = $user->staffReports()->with(['user', 'reviewer'])->latest()->take(5)->get();
        }
        
        $stats = [
            'total_reports' => $totalReports,
            'pending_reviews' => $pendingReviews,
            'reviewed_reports' => $reviewedReports,
            'draft_reports' => $draftReports,
            'submitted_reports' => $pendingReviews, // Alias untuk konsistensi
            'total_hours' => number_format($totalHours, 1),
            'draft_percentage' => $totalReports > 0 ? round(($draftReports / $totalReports) * 100, 1) : 0,
            'submitted_percentage' => $totalReports > 0 ? round(($pendingReviews / $totalReports) * 100, 1) : 0,
            'reviewed_percentage' => $totalReports > 0 ? round(($reviewedReports / $totalReports) * 100, 1) : 0,
        ];
        
        // Data untuk export dan bulk actions
        $users = User::where('role', 'petugas')->get();
        
        return view('staff-reports.index', compact('staffReports', 'stats', 'recentReports', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pastikan hanya petugas yang dapat membuat laporan
        if (!Auth::user()->isPetugas()) {
            abort(403);
        }
        
        return view('staff-reports.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Pastikan hanya petugas yang dapat membuat laporan
        if (!Auth::user()->isPetugas()) {
            abort(403);
        }
        
        $request->validate([
            'report_date' => 'required|date',
            'activities' => 'required|string',
            'challenges' => 'nullable|string',
            'hours_worked' => 'required|numeric|min:0.5|max:24',
            'status' => 'required|in:draft,submitted',
        ]);
        
        $report = StaffReport::create([
            'user_id' => Auth::id(),
            'report_date' => $request->report_date,
            'activities' => $request->activities,
            'challenges' => $request->challenges,
            'hours_worked' => $request->hours_worked,
            'status' => $request->status,
        ]);
        
        // Jika status submitted, kirim notifikasi ke admin
        if ($request->status === 'submitted') {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'staff_report_submitted',
                    'title' => 'Laporan Kerja Baru',
                    'message' => "Petugas " . Auth::user()->name . " telah mengirimkan laporan kerja.",
                    'data' => json_encode(['staff_report_id' => $report->id]),
                ]);
            }
        }
        
        \App\Models\ActivityLog::log('create', 'staff_report', 'Menambah laporan petugas: ' . $report->id);
        
        return redirect()->route('staff-reports.show', $report)
            ->with('success', 'Laporan kerja berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StaffReport $staffReport)
    {
        $user = Auth::user();
        
        // Pastikan hanya user pemilik atau admin yang dapat melihat laporan
        if (!$user->isAdmin() && $staffReport->user_id !== $user->id) {
            abort(403);
        }
        
        return view('staff-reports.show', compact('staffReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaffReport $staffReport)
    {
        $user = Auth::user();
        
        // Pastikan hanya user pemilik yang dapat edit laporan draft
        if ($staffReport->user_id !== $user->id || $staffReport->status !== 'draft') {
            abort(403);
        }
        
        return view('staff-reports.edit', compact('staffReport'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StaffReport $staffReport)
    {
        $user = Auth::user();
        
        // Pastikan hanya user pemilik yang dapat update laporan draft
        if ($staffReport->user_id !== $user->id || $staffReport->status !== 'draft') {
            abort(403);
        }
        
        $request->validate([
            'report_date' => 'required|date',
            'activities' => 'required|string',
            'challenges' => 'nullable|string',
            'hours_worked' => 'required|numeric|min:0.5|max:24',
            'status' => 'required|in:draft,submitted',
        ]);
        
        $staffReport->update([
            'report_date' => $request->report_date,
            'activities' => $request->activities,
            'challenges' => $request->challenges,
            'hours_worked' => $request->hours_worked,
            'status' => $request->status,
        ]);
        
        // Jika status diubah ke submitted, kirim notifikasi ke admin
        if ($request->status === 'submitted' && $staffReport->getOriginal('status') === 'draft') {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'staff_report_submitted',
                    'title' => 'Laporan Kerja Baru',
                    'message' => "Petugas " . $user->name . " telah mengirimkan laporan kerja.",
                    'data' => json_encode(['staff_report_id' => $staffReport->id]),
                ]);
            }
        }
        
        \App\Models\ActivityLog::log('update', 'staff_report', 'Mengedit laporan petugas: ' . $staffReport->id);
        
        return redirect()->route('staff-reports.show', $staffReport)
            ->with('success', 'Laporan kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaffReport $staffReport)
    {
        $user = Auth::user();
        
        // Pastikan hanya user pemilik yang dapat menghapus laporan draft
        // atau admin dapat menghapus laporan
        if ((!$user->isAdmin() && $staffReport->user_id !== $user->id) || 
            (!$user->isAdmin() && $staffReport->status !== 'draft')) {
            abort(403);
        }
        
        $staffReport->delete();
        
        \App\Models\ActivityLog::log('delete', 'staff_report', 'Menghapus laporan petugas: ' . $staffReport->id);
        
        return redirect()->route('staff-reports.index')
            ->with('success', 'Laporan kerja berhasil dihapus.');
    }

    /**
     * Review the staff report (admin only)
     */
    public function review(Request $request, StaffReport $staffReport)
    {
        // Pastikan hanya admin yang dapat me-review laporan
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        // Pastikan laporan sudah submitted
        if ($staffReport->status !== 'submitted') {
            return back()->with('error', 'Hanya laporan yang sudah disubmit yang dapat di-review.');
        }
        
        $request->validate([
            'review_notes' => 'required|string',
        ]);
        
        $staffReport->update([
            'status' => 'reviewed',
            'review_notes' => $request->review_notes,
            'reviewed_by' => Auth::id(),
        ]);
        
        // Kirim notifikasi ke petugas
        Notification::create([
            'user_id' => $staffReport->user_id,
            'type' => 'staff_report_reviewed',
            'title' => 'Laporan Kerja Telah Diulas',
            'message' => "Laporan kerja Anda tanggal " . $staffReport->report_date->format('d M Y') . " telah diulas.",
            'data' => json_encode(['staff_report_id' => $staffReport->id]),
        ]);
        
        return redirect()->route('staff-reports.show', $staffReport)
            ->with('success', 'Laporan kerja berhasil diulas.');
    }

    /**
     * Display the dashboard with statistics
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $totalReports = StaffReport::count();
            $pendingReviews = StaffReport::where('status', 'submitted')->count();
            $reviewedReports = StaffReport::where('status', 'reviewed')->count();
            $draftReports = StaffReport::where('status', 'draft')->count();
            $totalHours = StaffReport::sum('hours_worked');
            $recentReports = StaffReport::with(['user', 'reviewer'])->latest()->take(5)->get();
        } else {
            $totalReports = $user->staffReports()->count();
            $pendingReviews = $user->staffReports()->where('status', 'submitted')->count();
            $reviewedReports = $user->staffReports()->where('status', 'reviewed')->count();
            $draftReports = $user->staffReports()->where('status', 'draft')->count();
            $totalHours = $user->staffReports()->sum('hours_worked');
            $recentReports = $user->staffReports()->with(['user', 'reviewer'])->latest()->take(5)->get();
        }
        
        $stats = [
            'total_reports' => $totalReports,
            'pending_reviews' => $pendingReviews,
            'reviewed_reports' => $reviewedReports,
            'draft_reports' => $draftReports,
            'total_hours' => number_format($totalHours, 1),
            'draft_percentage' => $totalReports > 0 ? round(($draftReports / $totalReports) * 100, 1) : 0,
            'submitted_percentage' => $totalReports > 0 ? round(($pendingReviews / $totalReports) * 100, 1) : 0,
            'reviewed_percentage' => $totalReports > 0 ? round(($reviewedReports / $totalReports) * 100, 1) : 0,
        ];
        
        return view('staff-reports.dashboard', compact('stats', 'recentReports'));
    }

    /**
     * Display export page with filters
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $query = StaffReport::with(['user', 'reviewer']);
        
        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('report_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('report_date', '<=', $request->end_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // If not admin, only show own reports
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        $staffReports = $query->latest()->paginate(15);
        $users = User::where('role', 'petugas')->get();
        
        return view('staff-reports.export', compact('staffReports', 'users'));
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $query = StaffReport::with(['user', 'reviewer']);
        
        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('report_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('report_date', '<=', $request->end_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // If not admin, only show own reports
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        $staffReports = $query->latest()->get();
        
        $pdf = \PDF::loadView('staff-reports.pdf', compact('staffReports'));
        
        return $pdf->download('laporan-staff-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Get filtered reports for export tab
     */
    public function getFilteredReports(Request $request)
    {
        $user = Auth::user();
        $query = StaffReport::with(['user', 'reviewer']);
        
        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('report_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('report_date', '<=', $request->end_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // If not admin, only show own reports
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        $staffReports = $query->latest()->paginate(15);
        
        return response()->json([
            'html' => view('staff-reports.partials.export-table', compact('staffReports'))->render()
        ]);
    }

    /**
     * Get filtered reports for bulk actions tab
     */
    public function getBulkFilteredReports(Request $request)
    {
        // Pastikan hanya admin yang dapat mengakses
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $query = StaffReport::with(['user', 'reviewer']);
        
        // Apply filters
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }
        
        if ($request->filled('user_filter')) {
            $query->where('user_id', $request->user_filter);
        }
        
        if ($request->filled('date_from')) {
            $query->where('report_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('report_date', '<=', $request->date_to);
        }
        
        $staffReports = $query->latest()->paginate(15);
        
        return response()->json([
            'html' => view('staff-reports.partials.bulk-table', compact('staffReports'))->render()
        ]);
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $query = StaffReport::with(['user', 'reviewer']);
        
        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('report_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('report_date', '<=', $request->end_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // If not admin, only show own reports
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        $staffReports = $query->latest()->get();
        
        return \Excel::download(new \App\Exports\StaffReportsExport($staffReports), 'laporan-staff-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Display bulk actions page
     */
    public function bulkActions(Request $request)
    {
        // Pastikan hanya admin yang dapat mengakses
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $query = StaffReport::with(['user', 'reviewer']);
        
        // Apply filters
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }
        
        if ($request->filled('user_filter')) {
            $query->where('user_id', $request->user_filter);
        }
        
        if ($request->filled('date_from')) {
            $query->where('report_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('report_date', '<=', $request->date_to);
        }
        
        $staffReports = $query->latest()->paginate(15);
        $users = User::where('role', 'petugas')->get();
        
        return view('staff-reports.bulk-actions', compact('staffReports', 'users'));
    }

    /**
     * Process bulk actions
     */
    public function bulkProcess(Request $request)
    {
        // Pastikan hanya admin yang dapat mengakses
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $request->validate([
            'bulk_action' => 'required|in:approve_all,reject_all,delete_selected,export_selected',
            'selected_reports' => 'required|array|min:1',
            'selected_reports.*' => 'exists:staff_reports,id'
        ]);
        
        $selectedReports = StaffReport::whereIn('id', $request->selected_reports)->get();
        
        switch ($request->bulk_action) {
            case 'approve_all':
                foreach ($selectedReports as $report) {
                    if ($report->status === 'submitted') {
                        $report->update([
                            'status' => 'reviewed',
                            'review_notes' => 'Disetujui secara massal oleh admin',
                            'reviewed_by' => Auth::id(),
                        ]);
                        
                        // Kirim notifikasi
                        Notification::create([
                            'user_id' => $report->user_id,
                            'type' => 'staff_report_reviewed',
                            'title' => 'Laporan Kerja Telah Diulas',
                            'message' => "Laporan kerja Anda tanggal " . $report->report_date->format('d M Y') . " telah diulas.",
                            'data' => json_encode(['staff_report_id' => $report->id]),
                        ]);
                    }
                }
                $message = count($selectedReports) . ' laporan berhasil disetujui';
                break;
                
            case 'reject_all':
                foreach ($selectedReports as $report) {
                    if ($report->status === 'submitted') {
                        $report->update([
                            'status' => 'draft',
                            'review_notes' => 'Ditolak secara massal oleh admin',
                            'reviewed_by' => Auth::id(),
                        ]);
                        
                        // Kirim notifikasi
                        Notification::create([
                            'user_id' => $report->user_id,
                            'type' => 'staff_report_rejected',
                            'title' => 'Laporan Kerja Ditolak',
                            'message' => "Laporan kerja Anda tanggal " . $report->report_date->format('d M Y') . " telah ditolak.",
                            'data' => json_encode(['staff_report_id' => $report->id]),
                        ]);
                    }
                }
                $message = count($selectedReports) . ' laporan berhasil ditolak';
                break;
                
            case 'delete_selected':
                foreach ($selectedReports as $report) {
                    $report->delete();
                }
                $message = count($selectedReports) . ' laporan berhasil dihapus';
                break;
                
            case 'export_selected':
                return $this->exportSelected($selectedReports);
        }
        
        \App\Models\ActivityLog::log('bulk_action', 'staff_report', 'Aksi massal: ' . $request->bulk_action . ' - ' . count($selectedReports) . ' laporan');
        
        return redirect()->route('staff-reports.bulk-actions')->with('success', $message);
    }

    /**
     * Export selected reports
     */
    private function exportSelected($reports)
    {
        $pdf = \PDF::loadView('staff-reports.pdf', compact('reports'));
        return $pdf->download('laporan-staff-terpilih-' . date('Y-m-d') . '.pdf');
    }
}
