<?php

use App\Http\Controllers\StockControllers;
use App\Http\Controllers\DescriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// ─── Dashboard ───────────────────────────────────────────────────────────────
Route::get('/dashboard', [UserController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/home', [UserController::class, 'userDashboard'])
    ->middleware(['auth'])
    ->name('user.dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

// ─── Gestion des Stocks ───────────────────────────────────────────────────────
Route::prefix('stock')->middleware(['auth'])->name('stock.')->group(function () {

    // ⚠️ IMPORTANT : Les routes statiques AVANT les routes dynamiques /{stock}
    // Sinon Laravel interprète "create" et "inventaire" comme des IDs de stock

    // 1. Routes statiques (sans paramètre)
    Route::get('/inventaire', [StockControllers::class, 'inventaire'])->name('inventaire');
    Route::get('/', [StockControllers::class, 'index'])->name('index');
    Route::get('/create', [StockControllers::class, 'create'])->name('create');
    Route::post('/', [StockControllers::class, 'store'])->name('store');

    // 2. Routes des descriptions (statiques aussi, avant /{stock})
    Route::get('/description/create/{stock_id}', [DescriptionController::class, 'createdescription'])
        ->name('description.create');
    Route::post('/description/store/{id_stock}', [DescriptionController::class, 'store'])
        ->name('description.store');

    // 3. Routes dynamiques APRÈS (/{stock} capte tout ce qui reste)
    Route::get('/{stock}', [StockControllers::class, 'show'])->name('show');
    Route::get('/{stock}/edit', [StockControllers::class, 'edit'])->name('edit');
    Route::put('/{stock}', [StockControllers::class, 'update'])->name('update');
    Route::delete('/{stock}', [StockControllers::class, 'destroy'])->name('destroy');
});

// ─── Suppression description (hors prefix) ────────────────────────────────────
Route::delete('/description/{description}', [DescriptionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('description.destroy');

// ─── Espace Administration ────────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {

        Route::middleware(['role:admin,super_admin'])->group(function () {

            Route::get('/dashboard', [VenteController::class, 'dashboard'])
                ->name('vente.dashboard');

            Route::get('/vente', [VenteController::class, 'index'])->name('vente.index');
            Route::get('/vente/create', [VenteController::class, 'create'])->name('vente.create');
            Route::post('/vente', [VenteController::class, 'store'])->name('vente.store');
            Route::delete('/vente/{vente}', [VenteController::class, 'destroy'])->name('vente.destroy');
        });

        Route::middleware(['role:super_admin'])->group(function () {
            Route::get('/super/dashboard', fn() => view('admin.super.dashboard'))
                ->name('super.dashboard');
        });
    });
// routes/web.php


// Redirection intelligente après login
Route::get('/dashboard', [UserController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Dashboard Client
Route::middleware(['auth', 'role:0'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [UserController::class, 'clientDashboard'])->name('dashboard');
    });

// Dashboard Admin
Route::middleware(['auth', 'role:1'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [UserController::class, 'adminDashboard'])->name('dashboard');
    });
