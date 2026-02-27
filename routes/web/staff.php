<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\BorrowExtensionController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\StaffReportController;
use Illuminate\Support\Facades\Route;

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

    // Export PDF untuk maintenances
    Route::get('/maintenances/export/pdf', [MaintenanceController::class, 'exportPdf'])->name('maintenances.export.pdf');
});

// Rute Laporan Staff
Route::middleware(['auth'])->prefix('staff-reports')->name('staff-reports.')->group(function () {

    // == Rute Statis (tanpa parameter) ==
    Route::middleware('role:petugas')->group(function () {
        Route::get('/create', [StaffReportController::class, 'create'])->name('create');
        Route::post('/', [StaffReportController::class, 'store'])->name('store');
    });

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

    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/', [StaffReportController::class, 'index'])->name('index');
        Route::get('/dashboard', [StaffReportController::class, 'dashboard'])->name('dashboard');
    });

    // == Rute dengan Parameter ==
    Route::middleware('role:petugas')->group(function () {
        Route::get('/{staffReport}/edit', [StaffReportController::class, 'edit'])->name('edit');
        Route::patch('/{staffReport}', [StaffReportController::class, 'update'])->name('update');
        Route::delete('/{staffReport}', [StaffReportController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/{staffReport}/review', [StaffReportController::class, 'reviewForm'])->name('review.form');
        Route::post('/{staffReport}/review', [StaffReportController::class, 'review'])->name('review');
    });

    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/{staffReport}', [StaffReportController::class, 'show'])->name('show');
    });
});
