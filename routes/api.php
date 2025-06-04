<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TransactionController;

Route::prefix('inventory')->group(function () {
    Route::get('/', [InventoryController::class, 'index']);
    Route::get('/{id}', [InventoryController::class, 'show']);
    Route::put('/{id}/update-quantity', [InventoryController::class, 'updateQuantity']);
});

Route::get('/products/{id}', [ProductController::class, 'show']);

Route::post('/transactions/sale', [TransactionController::class, 'processSale']);

