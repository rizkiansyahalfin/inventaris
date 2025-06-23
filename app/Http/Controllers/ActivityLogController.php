<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class ActivityLogController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = \App\Models\ActivityLog::with('user');
        
        // Filter berdasarkan request
        $query->when($request->action, function ($query, $action) {
                return $query->where('action', $action);
            })
            ->when($request->model, function ($query, $model) {
                return $query->where('model', $model);
            })
            ->when($request->user_id, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                return $query->where('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                return $query->where('created_at', '<=', $dateTo . ' 23:59:59');
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('description', 'like', "%$search%");
            });

        $activityLogs = $query->orderBy('created_at', 'desc')->paginate(20);
        $users = \App\Models\User::orderBy('name')->get();
        
        // Log activity
        $filters = [];
        if ($request->action) $filters[] = 'action: ' . $request->action;
        if ($request->model) $filters[] = 'model: ' . $request->model;
        if ($request->user_id) $filters[] = 'user: ' . \App\Models\User::find($request->user_id)->name ?? 'Unknown';
        if ($request->date_from) $filters[] = 'dari tanggal: ' . $request->date_from;
        if ($request->date_to) $filters[] = 'sampai tanggal: ' . $request->date_to;
        
        $filterDescription = !empty($filters) ? 'Lihat log aktivitas dengan filter: ' . implode(', ', $filters) : 'Lihat log aktivitas';
        \App\Models\ActivityLog::log('view', 'activity_log', $filterDescription . ' (' . $activityLogs->total() . ' log)');
        
        return view('activity-logs.index', compact('activityLogs', 'users'));
    }

    public function export(Request $request)
    {
        $query = \App\Models\ActivityLog::with('user');
        $query->when($request->action, function ($query, $action) {
                return $query->where('action', $action);
            })
            ->when($request->model, function ($query, $model) {
                return $query->where('model', $model);
            })
            ->when($request->user_id, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                return $query->where('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                return $query->where('created_at', '<=', $dateTo . ' 23:59:59');
            });

        $activityLogs = $query->orderBy('created_at', 'desc')->get();

        // Log activity
        \App\Models\ActivityLog::log('export', 'activity_log', 'Export log aktivitas ke Excel (' . $activityLogs->count() . ' log)');

        return \Excel::download(new \App\Exports\ActivityLogsExport($activityLogs), 'log-aktivitas-' . now()->format('Ymd_His') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = \App\Models\ActivityLog::with('user');
        $query->when($request->action, function ($query, $action) {
                return $query->where('action', $action);
            })
            ->when($request->model, function ($query, $model) {
                return $query->where('model', $model);
            })
            ->when($request->user_id, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                return $query->where('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                return $query->where('created_at', '<=', $dateTo . ' 23:59:59');
            });

        $activityLogs = $query->orderBy('created_at', 'desc')->get();

        // Log activity
        \App\Models\ActivityLog::log('export', 'activity_log', 'Export log aktivitas ke PDF (' . $activityLogs->count() . ' log)');

        $pdf = \PDF::loadView('activity-logs.pdf', compact('activityLogs'));
        return $pdf->download('log-aktivitas-' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = \App\Models\ActivityLog::with('user');
        $query->when($request->action, function ($query, $action) {
                return $query->where('action', $action);
            })
            ->when($request->model, function ($query, $model) {
                return $query->where('model', $model);
            })
            ->when($request->user_id, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                return $query->where('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                return $query->where('created_at', '<=', $dateTo . ' 23:59:59');
            });

        $activityLogs = $query->orderBy('created_at', 'desc')->get();

        // Log activity
        \App\Models\ActivityLog::log('export', 'activity_log', 'Export log aktivitas ke CSV (' . $activityLogs->count() . ' log)');

        return \Excel::download(new \App\Exports\ActivityLogsExport($activityLogs), 'log-aktivitas-' . now()->format('Ymd_His') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function show($id)
    {
        $log = \App\Models\ActivityLog::with('user')->findOrFail($id);
        $geo = null;
        if ($log->ip_address) {
            try {
                $response = @file_get_contents('https://ipapi.co/' . $log->ip_address . '/json/');
                if ($response) {
                    $geo = json_decode($response, true);
                }
            } catch (\Exception $e) {
                $geo = null;
            }
        }
        // Cek perubahan data sebelum-sesudah jika ada di description (misal: JSON)
        $before = null;
        $after = null;
        if ($log->description && strpos($log->description, '{"before":') !== false) {
            $desc = json_decode($log->description, true);
            $before = $desc['before'] ?? null;
            $after = $desc['after'] ?? null;
        }
        return view('activity-logs.show', compact('log', 'geo', 'before', 'after'));
    }
} 