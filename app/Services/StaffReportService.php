<?php

namespace App\Services;

use App\Models\StaffReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class StaffReportService
{
    /**
     * Get dashboard stats for either admin or staff user.
     *
     * @return array{stats: array, recentReports: \Illuminate\Database\Eloquent\Collection}
     */
    public function getDashboardStats(User $user): array
    {
        $baseQuery = $user->isAdmin()
            ? StaffReport::query()
            : $user->staffReports();

        $totalReports = (clone $baseQuery)->count();
        $pendingReviews = (clone $baseQuery)->where('status', 'submitted')->count();
        $reviewedReports = (clone $baseQuery)->where('status', 'reviewed')->count();
        $draftReports = (clone $baseQuery)->where('status', 'draft')->count();
        $totalHours = (clone $baseQuery)->sum('hours_worked');

        $recentReports = (clone $baseQuery)
            ->with(['user', 'reviewer'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_reports' => $totalReports,
            'pending_reviews' => $pendingReviews,
            'reviewed_reports' => $reviewedReports,
            'draft_reports' => $draftReports,
            'submitted_reports' => $pendingReviews,
            'total_hours' => number_format($totalHours, 1),
            'draft_percentage' => $totalReports > 0 ? round(($draftReports / $totalReports) * 100, 1) : 0,
            'submitted_percentage' => $totalReports > 0 ? round(($pendingReviews / $totalReports) * 100, 1) : 0,
            'reviewed_percentage' => $totalReports > 0 ? round(($reviewedReports / $totalReports) * 100, 1) : 0,
        ];

        return ['stats' => $stats, 'recentReports' => $recentReports];
    }

    /**
     * Apply common filters to a StaffReport query.
     */
    public function applyFilters(Builder $query, array $filters, User $user): Builder
    {
        if (!empty($filters['start_date'])) {
            $query->where('report_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('report_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Non-admin users can only see their own reports
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Apply bulk action filters to a StaffReport query.
     */
    public function applyBulkFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['status_filter'])) {
            $query->where('status', $filters['status_filter']);
        }

        if (!empty($filters['user_filter'])) {
            $query->where('user_id', $filters['user_filter']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('report_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('report_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Build a human-readable filter description for logging.
     */
    public function buildFilterDescription(array $filters): array
    {
        $descriptions = [];

        if (!empty($filters['start_date'])) {
            $descriptions[] = 'tanggal mulai: ' . $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $descriptions[] = 'tanggal akhir: ' . $filters['end_date'];
        }
        if (!empty($filters['status'])) {
            $descriptions[] = 'status: ' . $filters['status'];
        }
        if (!empty($filters['user_id'])) {
            $userName = User::find($filters['user_id'])->name ?? 'Unknown';
            $descriptions[] = 'petugas: ' . $userName;
        }

        return $descriptions;
    }
}
