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
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect()->route('login');
});

// Routes for all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Routes for regular users - View only
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    
    // User can create borrow requests
    Route::get('/borrows/create', [BorrowController::class, 'create'])->name('borrows.create');
    Route::post('/borrows', [BorrowController::class, 'store'])->name('borrows.store');
    
    // User can view their own borrows
    Route::get('/borrows', [BorrowController::class, 'index'])->name('borrows.index');
    Route::get('/borrows/{borrow}', [BorrowController::class, 'show'])->name('borrows.show');
});

// Routes for petugas and admin
Route::middleware(['auth', 'role:petugas,admin'])->group(function () {
    // Full access to items management
    Route::resource('items', ItemController::class)->except(['index', 'show']);
    Route::get('/items/{item}/add-stock', [ItemController::class, 'showAddStockForm'])->name('items.add-stock.form');
    Route::post('/items/{item}/add-stock', [ItemController::class, 'addStock'])->name('items.add-stock');
    
    // Full access to maintenance
    Route::resource('maintenances', MaintenanceController::class);
    
    // Manage borrows (update status)
    Route::post('/borrows/{borrow}/status', [BorrowController::class, 'updateStatus'])->name('borrows.update_status');
    Route::resource('borrows', BorrowController::class)->except(['index', 'show', 'create', 'store']);
    
    // Access to resource attachments
    Route::resource('attachments', AttachmentController::class)->only(['destroy']);
    
    // Basic reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Routes for admin only
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Category management (admin only)
    Route::resource('categories', CategoryController::class);
    
    // Full reports access with export
    Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');
    
    // User Management
    Route::prefix('admin')->name('admin.')->group(function() {
        Route::resource('users', UserManagementController::class);
        Route::get('users/{user}/reset-password', [UserManagementController::class, 'showResetPasswordForm'])->name('users.reset-password.form');
        Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        Route::patch('users/{user}/update-role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
    });
});
