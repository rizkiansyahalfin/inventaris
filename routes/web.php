<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
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

require __DIR__ . '/auth.php';

Route::get('/', function () {
    return redirect()->route('login');
});

// Include Split Routes
require __DIR__ . '/web/staff.php';
require __DIR__ . '/web/admin.php';

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
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
});
