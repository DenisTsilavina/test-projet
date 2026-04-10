<?php

use App\Http\Controllers\StockControllers;
use App\Http\Controllers\DescriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [UserController::class, 'index'])->name('home');
Route::get('/dashboard', [UserController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

require __DIR__.'/auth.php';

Auth::routes();
// Admin
Route::get('/admin/vente/dashboard', [VenteController::class, 'dashboard'])
    ->middleware('auth')
    ->name('admin.vente.dashboard');

// Client
Route::get('/client/dashboard', [ClientController::class, 'index'])
    ->middleware('auth')
    ->name('client.dashboard');
Route::get('/dashboard', [StockControllers::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard.redirect');

// Stocks
/**
Route::get('/stocks', [StockControllers::class, 'index'])
    ->middleware(['auth'])
    ->name('stocks.index');
*/

Route::prefix('stock')->group(function () {
    Route::get('/stocks', [StockControllers::class, 'index'])->name('stock.index');
    Route::get('/stocks/create', [StockControllers::class, 'create'])->name('stock.create');
    Route::post('/stocks', [StockControllers::class, 'createStock'])->name('stock.store');
});

Route::prefix('stock')->group(function () {
    // Routes principales du Stock
    //Route::get('/stocks', [DescriptionController::class, 'index'])->name('stock.index');
    // Route pour créer une Description liée à un Stock
    // L'ID du stock est passé dans l'URL
    Route::post('/stock/description-complete/store/{id_stock}', [DescriptionController::class, 'store'])
        ->name('description.store');

    Route::get('/stock/create/description/{stock_id}', [DescriptionController::class, 'createdescription'])->name('description.create');
    Route::delete('/description/{description}', [DescriptionController::class, 'destroy'])->name('description.destroy');

    // route de modification description
    Route::get('/description/{id}/edit', [DescriptionController::class, 'descriptionUpdate'])->name('description.edit');
    Route::put('/description/{id}/update', [DescriptionController::class, 'update'])->name('description.update');
});

 // Vente
Route::prefix('admin')->name('admin.')->group(function () {

    // Tableau de bord
    Route::get('/vente/dashboard', [VenteController::class, 'dashboard'])
        ->name('vente.dashboard');

    // Liste / historique
    Route::get('/vente', [VenteController::class, 'index'])
        ->name('vente.index');

    // Formulaire création
    Route::get('/vente/create', [VenteController::class, 'create'])
        ->name('vente.create');

    // Enregistrer une vente
    Route::post('/vente', [VenteController::class, 'store'])
        ->name('vente.store');

    // Supprimer une vente
    Route::delete('/vente/{vente}', [VenteController::class, 'destroy'])
        ->name('vente.destroy');

    // Stock
       // Route::get('/stock', [StockControllers::class, 'index'])
        //->name('stock.index');

    });
