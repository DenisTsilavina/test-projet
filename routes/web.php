<?php

use App\Http\Controllers\ClientApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockControllers; // Vérifiez si votre contrôleur a bien un "s" à la fin
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommandeController;

/*
|--------------------------------------------------------------------------
| Routes Publiques & Authentification
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome');

Route::view('profile', 'profile')
    ->middleware('auth')
    ->name('profile');

require __DIR__.'/auth.php';

// Toutes les routes admin.* (users, dashboard, vente, commandes,
// super.dashboard) ET stock.* / description.* / souscategorie.* sont
// désormais dans routes/admin.php. web.php ne garde que ce qui n'est
// pas de l'administration : public, auth, espace client (Vue SPA), API.
require __DIR__.'/admin.php';

Route::post('/logout', [UserController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', [UserController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Espace Client
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:0'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [UserController::class, 'clientDashboard'])->name('dashboard');
    });

Route::middleware('auth')
    ->prefix('client/commande')
    ->name('commande.')
    ->group(function () {
        Route::get('/create',          [CommandeController::class, 'create'])->name('create');
        Route::post('/create',         [CommandeController::class, 'store'])->name('store');
        Route::get('/{commande}',      [CommandeController::class, 'show'])->name('show');
        Route::get('/{commande}/edit', [CommandeController::class, 'edit'])->name('edit');
        Route::put('/{commande}',      [CommandeController::class, 'update'])->name('update');
        Route::delete('/{commande}',   [CommandeController::class, 'destroy'])->name('destroy');
    });


/*
|--------------------------------------------------------------------------
| Routes "API" pour le front Vue (SPA)
|--------------------------------------------------------------------------
| IMPORTANT : doivent être déclarées AVANT le fallback SPA (Route::get('/{any}', ...))
| plus bas dans ce fichier, sinon ce dernier intercepte toutes les requêtes GET
| vers /api/* et renvoie le layout HTML au lieu du JSON attendu.
*/
Route::prefix('api')->middleware('auth')->group(function () {

    Route::get('/user', function () {
        return response()->json(['user' => Auth::user()]);
    });

    Route::get('/client/dashboard', [ClientApiController::class, 'dashboard']);

    Route::get('/commandes', [CommandeController::class, 'index']);
    Route::post('/commandes', [CommandeController::class, 'store']);
    Route::get('/commandes/{commande}', [CommandeController::class, 'show']);
    Route::put('/commandes/{commande}', [CommandeController::class, 'update']);
    Route::delete('/commandes/{commande}', [CommandeController::class, 'destroy']);

    Route::get('/stocks', [StockControllers::class, 'index']);
});


/*
|--------------------------------------------------------------------------
| Route Fallback Vue.js / Single Page Application (TOUJOURS EN DERNIER)
|--------------------------------------------------------------------------
*/
Route::get('/{any}', function () {
    return view('layouts.client');
})->where('any', '.*')->middleware('auth');
