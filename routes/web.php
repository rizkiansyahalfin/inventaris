<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttachmentController;
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

// Rute untuk semua pengguna yang sudah login
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rute profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rute untuk pengguna biasa - Hanya melihat
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    
    // Pengguna dapat membuat permintaan peminjaman
    Route::get('/borrows/create', [BorrowController::class, 'create'])->name('borrows.create');
    Route::post('/borrows', [BorrowController::class, 'store'])->name('borrows.store');
    
    // Pengguna dapat melihat peminjaman mereka sendiri
    Route::get('/borrows', [BorrowController::class, 'index'])->name('borrows.index');
    Route::get('/borrows/{borrow}', [BorrowController::class, 'show'])->name('borrows.show');
    
    // Rute notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Bookmark
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::patch('/bookmarks/{bookmark}', [BookmarkController::class, 'update'])->name('bookmarks.update');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
    Route::post('/items/{item}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    
    // Perpanjangan Peminjaman
    Route::get('/borrows/{borrow}/extend', [BorrowExtensionController::class, 'create'])->name('borrows.extend');
    Route::post('/borrows/{borrow}/extend', [BorrowExtensionController::class, 'store'])->name('borrows.extend.store');
    Route::get('/extensions', [BorrowExtensionController::class, 'index'])->name('extensions.index');
    Route::get('/extensions/{extension}', [BorrowExtensionController::class, 'show'])->name('extensions.show');
    
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
    Route::patch('/item-requests/{itemRequest}', [ItemRequestController::class, 'update'])->name('item-requests.update');
    Route::delete('/item-requests/{itemRequest}', [ItemRequestController::class, 'destroy'])->name('item-requests.destroy');
});

// Rute untuk petugas dan admin
Route::middleware(['auth', 'role:petugas,admin'])->group(function () {
    // Akses penuh ke manajemen barang
    Route::resource('items', ItemController::class)->except(['index', 'show']);
    Route::get('/items/{item}/add-stock', [ItemController::class, 'showAddStockForm'])->name('items.add-stock.form');
    Route::post('/items/{item}/add-stock', [ItemController::class, 'addStock'])->name('items.add-stock');
    
    // Akses penuh ke perawatan
    Route::resource('maintenances', MaintenanceController::class);
    
    // Kelola peminjaman (update status)
    Route::post('/borrows/{borrow}/status', [BorrowController::class, 'updateStatus'])->name('borrows.update_status');
    Route::resource('borrows', BorrowController::class)->except(['index', 'show', 'create', 'store']);
    
    // Akses ke lampiran
    Route::resource('attachments', AttachmentController::class)->only(['destroy']);
    
    // Laporan dasar
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Kelola Perpanjangan
    Route::post('/extensions/{extension}/status', [BorrowExtensionController::class, 'updateStatus'])->name('extensions.update_status');
    
    // Kelola Permintaan Barang
    Route::post('/item-requests/{itemRequest}/status', [ItemRequestController::class, 'updateStatus'])->name('item-requests.update_status');
    
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
    
    // Laporan Staff (khusus petugas)
    Route::middleware(['role:petugas'])->group(function () {
        Route::get('/staff-reports', [StaffReportController::class, 'index'])->name('staff-reports.index');
        Route::get('/staff-reports/create', [StaffReportController::class, 'create'])->name('staff-reports.create');
        Route::post('/staff-reports', [StaffReportController::class, 'store'])->name('staff-reports.store');
        Route::get('/staff-reports/{staffReport}', [StaffReportController::class, 'show'])->name('staff-reports.show');
        Route::get('/staff-reports/{staffReport}/edit', [StaffReportController::class, 'edit'])->name('staff-reports.edit');
        Route::patch('/staff-reports/{staffReport}', [StaffReportController::class, 'update'])->name('staff-reports.update');
        Route::delete('/staff-reports/{staffReport}', [StaffReportController::class, 'destroy'])->name('staff-reports.destroy');
    });
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
    });

    // Log Aktivitas
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    // Konfigurasi Sistem
    Route::resource('system-configs', SystemConfigController::class);
    
    // Review laporan staff
    Route::get('/staff-reports', [StaffReportController::class, 'index'])->name('staff-reports.index');
    Route::get('/staff-reports/{staffReport}', [StaffReportController::class, 'show'])->name('staff-reports.show');
    Route::post('/staff-reports/{staffReport}/review', [StaffReportController::class, 'review'])->name('staff-reports.review');
});
