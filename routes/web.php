<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\BorrowExtensionController;
use App\Http\Controllers\ItemFeedbackController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StaffReportController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SystemConfigController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect()->route('login');
});

// Untuk semua user yang sudah login (user, petugas, admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Barang (lihat & detail)
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

    // Peminjaman (buat & lihat milik sendiri)
    Route::get('/borrows/create', [BorrowController::class, 'create'])->name('borrows.create');
    Route::post('/borrows', [BorrowController::class, 'store'])->name('borrows.store');
    Route::get('/borrows', [BorrowController::class, 'index'])->name('borrows.index');
    Route::get('/borrows/{borrow}', [BorrowController::class, 'show'])->name('borrows.show');

    // Bookmark
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::patch('/bookmarks/{bookmark}', [BookmarkController::class, 'update'])->name('bookmarks.update');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
    Route::post('/items/{item}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');

    // Feedback
    Route::get('/feedbacks', [ItemFeedbackController::class, 'index'])->name('feedbacks.index');
    Route::get('/borrows/{borrow}/feedback', [ItemFeedbackController::class, 'create'])->name('feedbacks.create');
    Route::post('/borrows/{borrow}/feedback', [ItemFeedbackController::class, 'store'])->name('feedbacks.store');
    Route::get('/feedbacks/{feedback}', [ItemFeedbackController::class, 'show'])->name('feedbacks.show');
    Route::get('/feedbacks/{feedback}/edit', [ItemFeedbackController::class, 'edit'])->name('feedbacks.edit');
    Route::patch('/feedbacks/{feedback}', [ItemFeedbackController::class, 'update'])->name('feedbacks.update');
    Route::delete('/feedbacks/{feedback}', [ItemFeedbackController::class, 'destroy'])->name('feedbacks.destroy');

    // Permintaan Barang
    Route::get('/item-requests', [ItemRequestController::class, 'index'])->name('item-requests.index');
    Route::get('/item-requests/create', [ItemRequestController::class, 'create'])->name('item-requests.create');
    Route::post('/item-requests', [ItemRequestController::class, 'store'])->name('item-requests.store');
    Route::get('/item-requests/{itemRequest}', [ItemRequestController::class, 'show'])->name('item-requests.show');
    Route::get('/item-requests/{itemRequest}/edit', [ItemRequestController::class, 'edit'])->name('item-requests.edit');
    Route::delete('/item-requests/{itemRequest}', [ItemRequestController::class, 'destroy'])->name('item-requests.destroy');
    Route::match(['put', 'patch'], '/item-requests/{itemRequest}', [ItemRequestController::class, 'update'])->name('item-requests.update');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Untuk PETUGAS & ADMIN (manajemen barang, approval, dsb)
Route::middleware(['auth', 'role:petugas,admin'])->group(function () {
    // Manajemen barang
    Route::resource('items', ItemController::class)->except(['index', 'show']);
    Route::get('/items/{item}/add-stock', [ItemController::class, 'showAddStockForm'])->name('items.add-stock.form');
    Route::post('/items/{item}/add-stock', [ItemController::class, 'addStock'])->name('items.add-stock');

    // Tambahkan resource route untuk maintenances
    Route::resource('maintenances', MaintenanceController::class);

    // Perpanjangan Peminjaman
    Route::get('/borrows/{borrow}/extend', [BorrowExtensionController::class, 'create'])->name('borrows.extend');
    Route::post('/borrows/{borrow}/extend', [BorrowExtensionController::class, 'store'])->name('borrows.extend.store');
    Route::get('/extensions', [BorrowExtensionController::class, 'index'])->name('extensions.index');
    Route::get('/extensions/{extension}', [BorrowExtensionController::class, 'show'])->name('extensions.show');
    Route::post('/extensions/{extension}/status', [BorrowExtensionController::class, 'updateStatus'])->name('extensions.update_status');

    // Kelola peminjaman (update status, approval, dsb)
    Route::post('/borrows/{borrow}/status', [BorrowController::class, 'updateStatus'])->name('borrows.update_status');
    Route::post('/borrows/{borrow}/approve', [BorrowController::class, 'approve'])->name('borrows.approve');
    Route::post('/borrows/{borrow}/reject', [BorrowController::class, 'reject'])->name('borrows.reject');
    Route::resource('borrows', BorrowController::class)->except(['index', 'show', 'create', 'store']);

    // Akses ke lampiran
    Route::resource('attachments', AttachmentController::class)->only(['destroy']);

    // Laporan dasar
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Kelola Permintaan Barang
    Route::post('/item-requests/{itemRequest}/status', [ItemRequestController::class, 'updateStatus'])->name('item-requests.update_status');
    Route::patch('/item-requests/{itemRequest}/approve', [ItemRequestController::class, 'approve'])->name('item-requests.approve');
    Route::patch('/item-requests/{itemRequest}/reject', [ItemRequestController::class, 'reject'])->name('item-requests.reject');

    // Stock Opname
    Route::get('/stock-opnames', [StockOpnameController::class, 'index'])->name('stock-opnames.index');
    Route::get('/stock-opnames/create', [StockOpnameController::class, 'create'])->name('stock-opnames.create');
    Route::post('/stock-opnames', [StockOpnameController::class, 'store'])->name('stock-opnames.store');
    Route::get('/stock-opnames/{stockOpname}', [StockOpnameController::class, 'show'])->name('stock-opnames.show');
    Route::get('/stock-opnames/{stockOpname}/edit', [StockOpnameController::class, 'edit'])->name('stock-opnames.edit');
    Route::patch('/stock-opnames/{stockOpname}', [StockOpnameController::class, 'update'])->name('stock-opnames.update');
    Route::delete('/stock-opnames/{stockOpname}', [StockOpnameController::class, 'destroy'])->name('stock-opnames.destroy');
    Route::post('/stock-opnames/{stockOpname}/start', [StockOpnameController::class, 'start'])->name('stock-opnames.start');
    Route::get('/stock-opnames/{stockOpname}/items', [StockOpnameController::class, 'itemsIndex'])->name('stock-opnames.items.index');
    Route::get('/stock-opnames/{stockOpname}/items/{item}', [StockOpnameController::class, 'checkItem'])->name('stock-opnames.items.check');
    Route::post('/stock-opnames/{stockOpname}/items/{item}', [StockOpnameController::class, 'saveItemCheck'])->name('stock-opnames.items.save');
    Route::post('/stock-opnames/{stockOpname}/complete', [StockOpnameController::class, 'complete'])->name('stock-opnames.complete');
});

// Rute khusus admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Manajemen kategori (khusus admin)
    Route::resource('categories', CategoryController::class);
    // Akses penuh ke laporan dengan ekspor
    Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');
    // Manajemen Pengguna
    Route::prefix('admin')->name('admin.')->group(function() {
        Route::resource('users', UserManagementController::class);
        Route::get('users/{user}/reset-password', [UserManagementController::class, 'showResetPasswordForm'])->name('users.reset-password.form');
        Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        Route::patch('users/{user}/update-role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
        Route::put('/users/{user}/status', [UserManagementController::class, 'updateStatus'])->name('users.update-status');
        // Approval Peminjaman
        Route::get('borrow-approvals', [App\Http\Controllers\Admin\BorrowApprovalController::class, 'index'])->name('borrow-approvals.index');
        Route::get('borrow-approvals/pending', [App\Http\Controllers\Admin\BorrowApprovalController::class, 'pending'])->name('borrow-approvals.pending');
        Route::get('borrow-approvals/approved', [App\Http\Controllers\Admin\BorrowApprovalController::class, 'approved'])->name('borrow-approvals.approved');
        Route::get('borrow-approvals/rejected', [App\Http\Controllers\Admin\BorrowApprovalController::class, 'rejected'])->name('borrow-approvals.rejected');
        Route::get('borrow-approvals/report', [App\Http\Controllers\Admin\BorrowApprovalController::class, 'report'])->name('borrow-approvals.report');
        Route::post('borrow-approvals/bulk-approve', [App\Http\Controllers\Admin\BorrowApprovalController::class, 'bulkApprove'])->name('borrow-approvals.bulk-approve');
        Route::post('borrow-approvals/bulk-reject', [App\Http\Controllers\Admin\BorrowApprovalController::class, 'bulkReject'])->name('borrow-approvals.bulk-reject');
    });
    // Log Aktivitas
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    // Konfigurasi Sistem
    Route::resource('system-configs', SystemConfigController::class);
});

// Rute Laporan Staff (dikelompokkan dan diurutkan dengan benar)
Route::middleware(['auth'])->prefix('staff-reports')->name('staff-reports.')->group(function () {

    // == Rute Statis (tanpa parameter) ==
    // Harus didefinisikan sebelum rute dengan parameter untuk menghindari konflik.

    // Rute untuk Petugas
    Route::middleware('role:petugas')->group(function () {
        Route::get('/create', [StaffReportController::class, 'create'])->name('create');
        Route::post('/', [StaffReportController::class, 'store'])->name('store');
    });

    // Rute untuk Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/export', [StaffReportController::class, 'export'])->name('export');
        Route::get('/export/pdf', [StaffReportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export/excel', [StaffReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/print', [StaffReportController::class, 'print'])->name('print');
        Route::get('/bulk-actions', [StaffReportController::class, 'bulkActions'])->name('bulk-actions');
        Route::post('/bulk-process', [StaffReportController::class, 'bulkProcess'])->name('bulk-process');
        Route::post('/filtered-reports', [StaffReportController::class, 'getFilteredReports'])->name('filtered-reports');
        Route::post('/bulk-filtered-reports', [StaffReportController::class, 'getBulkFilteredReports'])->name('bulk-filtered-reports');
    });
    
    // Rute untuk Admin dan Petugas
    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/', [StaffReportController::class, 'index'])->name('index');
        Route::get('/dashboard', [StaffReportController::class, 'dashboard'])->name('dashboard');
    });


    // == Rute dengan Parameter ==
    // Didefinisikan setelah rute statis.

    // Rute untuk Petugas
    Route::middleware('role:petugas')->group(function () {
        Route::get('/{staffReport}/edit', [StaffReportController::class, 'edit'])->name('edit');
        Route::patch('/{staffReport}', [StaffReportController::class, 'update'])->name('update');
        Route::delete('/{staffReport}', [StaffReportController::class, 'destroy'])->name('destroy');
    });

    // Rute untuk Admin
    Route::middleware('role:admin')->group(function() {
        Route::get('/{staffReport}/review', [StaffReportController::class, 'reviewForm'])->name('review.form');
        Route::post('/{staffReport}/review', [StaffReportController::class, 'review'])->name('review');
    });

    // Rute untuk Admin dan Petugas (paling umum, jadi diletakkan terakhir)
    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/{staffReport}', [StaffReportController::class, 'show'])->name('show');
    });
});
