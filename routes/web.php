<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Items
    Route::resource('items', ItemController::class);

    // Borrows
    Route::resource('borrows', BorrowController::class);
    Route::patch('borrows/{borrow}/return', [BorrowController::class, 'return'])->name('borrows.return');
});
