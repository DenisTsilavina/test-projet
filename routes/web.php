<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientRegisterController;

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
// super.dashboard) ET stock.* / description.* / souscategorie.* /
// achat.* / produit.* sont dans routes/admin.php.
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

// CHANGEMENT : le bloc 'client/commande' (create/store/show/edit/update/destroy
// via CommandeController) a été supprimé. CommandeController ne retourne plus
// de vues Blade (create()/edit() n'existent même plus dessus) — tout passe
// désormais par /api/commandes/* (routes/api.php) pour le frontend Vue.
//
// CHANGEMENT : le groupe Route::prefix('api')->middleware('auth')->group(...)
// qui existait ici a été supprimé entièrement. Il dupliquait/entrait en
// conflit avec routes/api.php (même URI /api/commandes, middleware différent
// - 'auth' session ici vs 'auth:sanctum' là-bas). routes/api.php est
// désormais la SEULE source de vérité pour les routes /api/*.


/*
|--------------------------------------------------------------------------
| Inscription publique des clients
|--------------------------------------------------------------------------
| Réservée aux visiteurs non connectés. Crée toujours un CLIENT.
| Doit rester AVANT le fallback SPA (/{any}) pour ne pas être avalée.
*/
Route::middleware('guest')->group(function () {
    Route::get('/inscription',  [ClientRegisterController::class, 'show'])->name('client.register');
    Route::post('/inscription', [ClientRegisterController::class, 'store'])->name('client.register.store');
});


/*
|--------------------------------------------------------------------------
| Route Fallback Vue.js / Single Page Application (TOUJOURS EN DERNIER)
|--------------------------------------------------------------------------
*/
Route::get('/{any}', function () {
    return view('layouts.client');
})->where('any', '.*')->middleware('auth');
