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

// ── Page d'accueil ──
Route::get('/', [UserController::class, 'index'])->name('home');

// ── Auth (login, register, logout) ──
require __DIR__.'/auth.php';

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');
// ── Routes protégées ──
Route::middleware('auth')->group(function () {

    // Dashboard user
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

    // Dashboard client
    Route::get('/client/dashboard', [ClientController::class, 'index'])->name('client.dashboard');

    // Page d'achat

    Route::get('/achat', [ClientController::class, 'achat'])->name('client.achat');

    // Créer un nouveau client
    Route::post('/client/create', [ClientController::class, 'createNewClient'])->name('client.createNewClient');

});


//                      ===============================
//                                    Stocks
//                      ==============================
    Route::prefix('stock')->group(function () {
        Route::get('/stocks', [StockControllers::class, 'index'])->name('stock.index');
        Route::get('/stocks/create', [StockControllers::class, 'create'])->name('stock.create');
        Route::post('/stocks', [StockControllers::class, 'createStock'])->name('stock.store');
    });

    Route::prefix('stock')->group(function () {

        Route::post('/stock/description-complete/store/{id_stock}', [DescriptionController::class, 'store'])
            ->name('description.store');

        Route::get('/stock/create/description/{stock_id}', [DescriptionController::class, 'createdescription'])->name('description.create');
        Route::delete('/description/{description}', [DescriptionController::class, 'destroy'])->name('description.destroy');

        // route de modification description
        Route::get('/description/{id}/edit', [DescriptionController::class, 'descriptionUpdate'])->name('description.edit');
        Route::put('/description/{id}/update', [DescriptionController::class, 'update'])->name('description.update');
    });
//                      ===============================
//                                    Vente
//                      ==============================
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
    });
    // ========================
    //          client
    // ========================


