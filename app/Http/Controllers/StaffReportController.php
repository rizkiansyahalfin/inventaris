<?php

namespace App\Http\Controllers;

use App\Models\StaffReport;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use App\Services\StaffReportService;
use App\Http\Requests\StoreStaffReportRequest;
use App\Http\Requests\UpdateStaffReportRequest;
use App\Exports\StaffReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffReportController extends Controller
{
    public function __construct(
        private StaffReportService $staffReportService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $staffReports = StaffReport::with(['user', 'reviewer'])->latest()->paginate(15);
        } else {
            $staffReports = $user->staffReports()->with('reviewer')->latest()->paginate(15);
        }

        $dashboardData = $this->staffReportService->getDashboardStats($user);
        $users = User::where('role', 'petugas')->get();

        ActivityLog::log('view', 'staff_report', 'Akses halaman utama laporan staff (Total: ' . $dashboardData['stats']['total_reports'] . ' laporan)');

        return view('staff-reports.index', compact('staffReports', 'users') + [
            'stats' => $dashboardData['stats'],
            'recentReports' => $dashboardData['recentReports'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->isPetugas()) {
            abort(403);
        }

        ActivityLog::log('view', 'staff_report', 'Akses halaman buat laporan staff baru');

        return view('staff-reports.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffReportRequest $request)
    {
        $report = StaffReport::create([
            'user_id' => Auth::id(),
            'report_date' => $request->report_date,
            'activities' => $request->activities,
            'challenges' => $request->challenges,
            'hours_worked' => $request->hours_worked,
            'status' => $request->status,
        ]);

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

        ActivityLog::log('create', 'staff_report', 'Menambah laporan petugas: ' . $report->id);

        return redirect()->route('staff-reports.show', $report)
            ->with('success', 'Laporan kerja berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StaffReport $staffReport)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && $staffReport->user_id !== $user->id) {
            abort(403);
        }

        ActivityLog::log('view', 'staff_report', 'Lihat detail laporan staff ID: ' . $staffReport->id . ' oleh ' . $staffReport->user->name);

        return view('staff-reports.show', compact('staffReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaffReport $staffReport)
    {
        $user = Auth::user();

        if ($staffReport->user_id !== $user->id || $staffReport->status !== 'draft') {
            abort(403);
        }

        ActivityLog::log('view', 'staff_report', 'Akses halaman edit laporan staff ID: ' . $staffReport->id);

        return view('staff-reports.edit', compact('staffReport'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStaffReportRequest $request, StaffReport $staffReport)
    {
        $user = Auth::user();

        $staffReport->update([
            'report_date' => $request->report_date,
            'activities' => $request->activities,
            'challenges' => $request->challenges,
            'hours_worked' => $request->hours_worked,
            'status' => $request->status,
        ]);

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

        ActivityLog::log('update', 'staff_report', 'Mengedit laporan petugas: ' . $staffReport->id);

        return redirect()->route('staff-reports.show', $staffReport)
            ->with('success', 'Laporan kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaffReport $staffReport)
    {
        $user = Auth::user();

        if (
            (!$user->isAdmin() && $staffReport->user_id !== $user->id) ||
            (!$user->isAdmin() && $staffReport->status !== 'draft')
        ) {
            abort(403);
        }

        $staffReport->delete();

        ActivityLog::log('delete', 'staff_report', 'Menghapus laporan petugas: ' . $staffReport->id);

        return redirect()->route('staff-reports.index')
            ->with('success', 'Laporan kerja berhasil dihapus.');
    }

    /**
     * Review the staff report (admin only)
     */
    public function review(Request $request, StaffReport $staffReport)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

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

        Notification::create([
            'user_id' => $staffReport->user_id,
            'type' => 'staff_report_reviewed',
            'title' => 'Laporan Kerja Telah Diulas',
            'message' => "Laporan kerja Anda tanggal " . \Carbon\Carbon::parse($staffReport->report_date)->format('d M Y') . " telah diulas.",
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
        $dashboardData = $this->staffReportService->getDashboardStats($user);

        ActivityLog::log('view', 'staff_report', 'Akses dashboard laporan staff (Total: ' . $dashboardData['stats']['total_reports'] . ', Pending: ' . $dashboardData['stats']['pending_reviews'] . ', Reviewed: ' . $dashboardData['stats']['reviewed_reports'] . ')');

        return view('staff-reports.dashboard', compact('dashboardData') + [
            'stats' => $dashboardData['stats'],
            'recentReports' => $dashboardData['recentReports'],
        ]);
    }

    /**
     * Display export page with filters
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $query = StaffReport::with(['user', 'reviewer']);

        $filters = $request->only(['start_date', 'end_date', 'status', 'user_id']);
        $this->staffReportService->applyFilters($query, $filters, $user);

        $staffReports = $query->latest()->paginate(15);
        $users = User::where('role', 'petugas')->get();

        ActivityLog::log('view', 'staff_report', 'Akses halaman export laporan staff');

        return view('staff-reports.export', compact('staffReports', 'users'));
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $query = StaffReport::with(['user', 'reviewer']);

        $filters = $request->only(['start_date', 'end_date', 'status', 'user_id']);
        $this->staffReportService->applyFilters($query, $filters, $user);
        $filterDescriptions = $this->staffReportService->buildFilterDescription($filters);

        $staffReports = $query->latest()->get();

        $filterDescription = !empty($filterDescriptions) ? 'Export PDF laporan staff dengan filter: ' . implode(', ', $filterDescriptions) : 'Export PDF semua laporan staff';
        ActivityLog::log('export', 'staff_report', $filterDescription . ' (' . $staffReports->count() . ' laporan)');

        $pdf = Pdf::loadView('staff-reports.pdf', compact('staffReports'));

        return $pdf->download('laporan-staff-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Get filtered reports for export tab
     */
    public function getFilteredReports(Request $request)
    {
        $user = Auth::user();
        $query = StaffReport::with(['user', 'reviewer']);

        $filters = $request->only(['start_date', 'end_date', 'status', 'user_id']);
        $this->staffReportService->applyFilters($query, $filters, $user);
        $filterDescriptions = $this->staffReportService->buildFilterDescription($filters);

        $staffReports = $query->latest()->paginate(15);

        $filterDescription = !empty($filterDescriptions) ? 'Filter laporan staff dengan: ' . implode(', ', $filterDescriptions) : 'Lihat semua laporan staff';
        ActivityLog::log('filter', 'staff_report', $filterDescription);

        return response()->json([
            'html' => view('staff-reports.partials.export-table', compact('staffReports'))->render(),
        ]);
    }

    /**
     * Get filtered reports for bulk actions tab
     */
    public function getBulkFilteredReports(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = StaffReport::with(['user', 'reviewer']);

        $filters = $request->only(['status_filter', 'user_filter', 'date_from', 'date_to']);
        $this->staffReportService->applyBulkFilters($query, $filters);

        $staffReports = $query->latest()->paginate(15);

        ActivityLog::log('filter', 'staff_report', 'Filter laporan untuk aksi massal');

        return response()->json([
            'html' => view('staff-reports.partials.bulk-table', compact('staffReports'))->render(),
        ]);
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $query = StaffReport::with(['user', 'reviewer']);

        $filters = $request->only(['start_date', 'end_date', 'status', 'user_id']);
        $this->staffReportService->applyFilters($query, $filters, $user);
        $filterDescriptions = $this->staffReportService->buildFilterDescription($filters);

        $staffReports = $query->latest()->get();

        $filterDescription = !empty($filterDescriptions) ? 'Export Excel laporan staff dengan filter: ' . implode(', ', $filterDescriptions) : 'Export Excel semua laporan staff';
        ActivityLog::log('export', 'staff_report', $filterDescription . ' (' . $staffReports->count() . ' laporan)');

        return Excel::download(new StaffReportsExport($staffReports), 'laporan-staff-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Display bulk actions page
     */
    public function bulkActions(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = StaffReport::with(['user', 'reviewer']);

        $filters = $request->only(['status_filter', 'user_filter', 'date_from', 'date_to']);
        $this->staffReportService->applyBulkFilters($query, $filters);

        $staffReports = $query->latest()->paginate(15);
        $users = User::where('role', 'petugas')->get();

        ActivityLog::log('view', 'staff_report', 'Akses halaman aksi massal laporan staff');

        return view('staff-reports.bulk-actions', compact('staffReports', 'users'));
    }

    /**
     * Process bulk actions
     */
    public function bulkProcess(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'bulk_action' => 'required|in:approve_all,reject_all,delete_selected,export_selected',
            'selected_reports' => 'required|array|min:1',
            'selected_reports.*' => 'exists:staff_reports,id',
        ]);

        /** @var \Illuminate\Database\Eloquent\Collection<int, StaffReport> $selectedReports */
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

                        Notification::create([
                            'user_id' => $report->user_id,
                            'type' => 'staff_report_reviewed',
                            'title' => 'Laporan Kerja Telah Diulas',
                            'message' => "Laporan kerja Anda tanggal " . \Carbon\Carbon::parse($report->report_date)->format('d M Y') . " telah diulas.",
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

                        Notification::create([
                            'user_id' => $report->user_id,
                            'type' => 'staff_report_rejected',
                            'title' => 'Laporan Kerja Ditolak',
                            'message' => "Laporan kerja Anda tanggal " . \Carbon\Carbon::parse($report->report_date)->format('d M Y') . " telah ditolak.",
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

        ActivityLog::log('bulk_action', 'staff_report', 'Aksi massal: ' . $request->bulk_action . ' - ' . count($selectedReports) . ' laporan');

        return redirect()->route('staff-reports.bulk-actions')->with('success', $message);
    }

    /**
     * Export selected reports
     */
    private function exportSelected($reports)
    {
        ActivityLog::log('export', 'staff_report', 'Export laporan staff terpilih (' . $reports->count() . ' laporan)');

        $pdf = Pdf::loadView('staff-reports.pdf', compact('reports'));
        return $pdf->download('laporan-staff-terpilih-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Show the review form for staff report
     */
    public function reviewForm(StaffReport $staffReport)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($staffReport->status !== 'submitted') {
            return back()->with('error', 'Hanya laporan yang sudah disubmit yang dapat di-review.');
        }

        ActivityLog::log('view', 'staff_report', 'Akses halaman review laporan staff ID: ' . $staffReport->id . ' oleh ' . $staffReport->user->name);

        return view('staff-reports.review', compact('staffReport'));
    }

    /**
     * Print staff reports
     */
    public function print(Request $request)
    {
        $user = Auth::user();
        $query = StaffReport::with(['user', 'reviewer']);

        $filters = $request->only(['start_date', 'end_date', 'status', 'user_id']);
        $this->staffReportService->applyFilters($query, $filters, $user);
        $filterDescriptions = $this->staffReportService->buildFilterDescription($filters);

        $staffReports = $query->latest()->get();

        $filterDescription = !empty($filterDescriptions) ? 'Print laporan staff dengan filter: ' . implode(', ', $filterDescriptions) : 'Print semua laporan staff';
        ActivityLog::log('print', 'staff_report', $filterDescription . ' (' . $staffReports->count() . ' laporan)');

        return view('staff-reports.print', compact('staffReports'));
    }
}
