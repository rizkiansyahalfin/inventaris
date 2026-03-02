<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\BorrowApprovalController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SystemConfigController;
use Illuminate\Support\Facades\Route;

// Rute khusus admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Manajemen kategori (khusus admin)
    Route::resource('categories', CategoryController::class);
    // Manajemen lokasi (khusus admin)
    Route::resource('locations', LocationController::class);
    // Akses penuh ke laporan dengan ekspor
    Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');
    // Manajemen Pengguna
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::get('users/{user}/reset-password', [UserManagementController::class, 'showResetPasswordForm'])->name('users.reset-password.form');
        Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        Route::patch('users/{user}/update-role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
        Route::put('/users/{user}/status', [UserManagementController::class, 'updateStatus'])->name('users.update-status');
        // Approval Peminjaman
        Route::get('borrow-approvals', [BorrowApprovalController::class, 'index'])->name('borrow-approvals.index');
        Route::get('borrow-approvals/pending', [BorrowApprovalController::class, 'pending'])->name('borrow-approvals.pending');
        Route::get('borrow-approvals/approved', [BorrowApprovalController::class, 'approved'])->name('borrow-approvals.approved');
        Route::get('borrow-approvals/rejected', [BorrowApprovalController::class, 'rejected'])->name('borrow-approvals.rejected');
        Route::get('borrow-approvals/report', [BorrowApprovalController::class, 'report'])->name('borrow-approvals.report');
        Route::post('borrow-approvals/bulk-approve', [BorrowApprovalController::class, 'bulkApprove'])->name('borrow-approvals.bulk-approve');
        Route::post('borrow-approvals/bulk-reject', [BorrowApprovalController::class, 'bulkReject'])->name('borrow-approvals.bulk-reject');
    });
    // Log Aktivitas
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::get('/activity-logs/export/pdf', [ActivityLogController::class, 'exportPdf'])->name('activity-logs.export-pdf');
    Route::get('/activity-logs/export/csv', [ActivityLogController::class, 'exportCsv'])->name('activity-logs.export-csv');
    Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    // Konfigurasi Sistem
    Route::resource('system-configs', SystemConfigController::class);
});
