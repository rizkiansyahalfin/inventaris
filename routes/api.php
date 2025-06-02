<?php

use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\BorrowController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ItemController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Categories
    Route::apiResource('categories', CategoryController::class);
    
    // Items
    Route::apiResource('items', ItemController::class);
    Route::post('items/{item}/categories/{category}', [ItemController::class, 'attachCategory']);
    Route::delete('items/{item}/categories/{category}', [ItemController::class, 'detachCategory']);
    
    // Attachments
    Route::post('attachments', [AttachmentController::class, 'store']);
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy']);
    
    // Borrows
    Route::apiResource('borrows', BorrowController::class);
    Route::patch('borrows/{borrow}/return', [BorrowController::class, 'return']);
}); 