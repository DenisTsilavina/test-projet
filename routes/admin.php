<?php

use App\Http\Controllers\StockControllers;
use App\Http\Controllers\DescriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;
use Illuminate\Support\Facades\Route;

// ── Routes protégées (admin) ──
Route::middleware(['auth'])->group(function () {

    // Dashboard admin
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

    // ========================
    //         Stocks
    // ========================
    Route::prefix('stock')->group(function () {
        Route::get('/stocks',        [StockControllers::class, 'index'])->name('stock.index');
        Route::get('/stocks/create', [StockControllers::class, 'create'])->name('stock.create');
        Route::post('/stocks',       [StockControllers::class, 'createStock'])->name('stock.store');
    });

    Route::prefix('stock')->group(function () {
        Route::post('/stock/description-complete/store/{id_stock}',
            [DescriptionController::class, 'store'])->name('description.store');
        Route::get('/stock/create/description/{stock_id}',
            [DescriptionController::class, 'createdescription'])->name('description.create');
        Route::delete('/description/{description}',
            [DescriptionController::class, 'destroy'])->name('description.destroy');
        Route::get('/description/{id}/edit',
            [DescriptionController::class, 'descriptionUpdate'])->name('description.edit');
        Route::put('/description/{id}/update',
            [DescriptionController::class, 'update'])->name('description.update');
    });

    // ========================
    //         Vente
    // ========================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/vente/dashboard', [VenteController::class, 'dashboard'])->name('vente.dashboard');
        Route::get('/vente',           [VenteController::class, 'index'])->name('vente.index');
        Route::get('/vente/create',    [VenteController::class, 'create'])->name('vente.create');
        Route::post('/vente',          [VenteController::class, 'store'])->name('vente.store');
        Route::delete('/vente/{vente}',[VenteController::class, 'destroy'])->name('vente.destroy');
    });
});
