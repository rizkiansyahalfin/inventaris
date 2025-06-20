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
            $reports = StaffReport::with(['user', 'reviewer'])
                ->latest()
                ->paginate(15);
        } else {
            $reports = $user->staffReports()
                ->with('reviewer')
                ->latest()
                ->paginate(15);
        }
        
        return view('staff_reports.index', compact('reports'));
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
        
        return view('staff_reports.create');
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
        
        return view('staff_reports.show', compact('staffReport'));
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
        
        return view('staff_reports.edit', compact('staffReport'));
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
}
