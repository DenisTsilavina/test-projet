<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\CommandeAdminController;
use App\Http\Controllers\StockControllers;
use App\Http\Controllers\DescriptionController;

/*
|--------------------------------------------------------------------------
| Routes Admin & Super Admin
|--------------------------------------------------------------------------
| Chargé depuis web.php via require, comme auth.php.
|
| CHANGEMENT : ce fichier regroupe désormais TOUTE la gestion admin —
| utilisateurs, dashboard, ventes, commandes, ET stocks / descriptions /
| sous-catégories (auparavant dans web.php). web.php ne garde plus que
| les routes publiques, l'auth, et l'espace client (Vue SPA).
| Les URLs elles-mêmes (/stock/..., /description/..., /souscategorie/...)
| ne changent pas — seul l'endroit où elles sont déclarées change.
*/

Route::prefix('admin/users')
    ->middleware(['auth', 'role:super_admin'])
    ->name('admin.users.')
    ->group(function () {
        Route::get('/',            [UserController::class, 'list'])->name('list');
        Route::get('/create',      [UserController::class, 'create'])->name('create');
        Route::post('/',           [UserController::class, 'store'])->name('store');
        Route::get('/{user}',      [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}',      [UserController::class, 'update'])->name('update');
        Route::delete('/{user}',   [UserController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {

        Route::get('/dashboard', [UserController::class, 'adminDashboard'])->name('dashboard');

        Route::get('/vente',            [VenteController::class, 'index'])->name('vente.index');
        Route::get('/vente/create',     [VenteController::class, 'create'])->name('vente.create');
        Route::post('/vente',           [VenteController::class, 'store'])->name('vente.store');
        Route::delete('/vente/{vente}', [VenteController::class, 'destroy'])->name('vente.destroy');

        Route::get('/commandes', [CommandeAdminController::class, 'index'])->name('commandes.index');
        Route::get('/commandes/{commande}', [CommandeAdminController::class, 'show'])->name('commandes.show');
        Route::patch('/commandes/{commande}/demander-infos', [CommandeAdminController::class, 'demanderInfos'])->name('commandes.demander-infos');
        Route::patch('/commandes/{commande}/confirmer', [CommandeAdminController::class, 'confirmer'])->name('commandes.confirmer');
        Route::patch('/commandes/{commande}/refuser', [CommandeAdminController::class, 'refuser'])->name('commandes.refuser');

        Route::middleware('role:super_admin')->group(function () {
            Route::get('/super/dashboard', function () {
                return view('admin.super.dashboard');
            })->name('super.dashboard');
        });
    });


/*
|--------------------------------------------------------------------------
| Gestion des Stocks & Sous-catégories
|--------------------------------------------------------------------------
| CHANGEMENT : déplacé depuis web.php. Le nom de route ('stock.', pas
| 'admin.stock.') et les URLs ('/stock/...') restent inchangés pour ne
| rien casser dans les vues existantes (route('stock.index') etc.) —
| seule leur déclaration change de fichier.
*/
Route::prefix('stock')
    ->middleware('auth')
    ->name('stock.')
    ->group(function () {

        // Lecture : accessible à tout utilisateur connecté (ex: Vendeur).
        Route::get('/',              [StockControllers::class, 'index'])->name('index');
        Route::get('/inventaire',    [StockControllers::class, 'inventaire'])->name('inventaire');

        // Écriture : réservée aux admins / super admins.
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::get('/create',        [StockControllers::class, 'create'])->name('create');
            Route::post('/',             [StockControllers::class, 'store'])->name('store');
            Route::get('/{stock}/edit',  [StockControllers::class, 'edit'])->name('edit');
            Route::put('/{stock}',       [StockControllers::class, 'update'])->name('update');
            Route::delete('/{stock}',    [StockControllers::class, 'destroy'])->name('destroy');
        });

        Route::get('/{stock}',       [StockControllers::class, 'show'])->name('show');
    });

// Description / sous-catégorie : entièrement des opérations d'écriture,
// réservées aux admins / super admins.
Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {

    Route::get('/stock/description/create/{stock_id}', [DescriptionController::class, 'create'])->name('description.create');
    Route::post('/stock/description', [DescriptionController::class, 'store'])->name('description.store');
    Route::get('/description/{description}/edit', [DescriptionController::class, 'edit'])->name('description.edit');
    Route::put('/description/{description}', [DescriptionController::class, 'update'])->name('description.update');
    Route::delete('/description/{description}', [DescriptionController::class, 'destroy'])->name('description.destroy');

    Route::get('/souscategorie/create/{description_id}', [DescriptionController::class, 'createSousCategorie'])->name('souscategorie.create');
    Route::post('/souscategorie', [DescriptionController::class, 'storeSousCategorie'])->name('souscategorie.store');
    Route::get('/souscategorie/{sousCategory}/edit', [DescriptionController::class, 'editSousCategorie'])->name('souscategorie.edit');
    Route::put('/souscategorie/{sousCategory}', [DescriptionController::class, 'updateSousCategorie'])->name('souscategorie.update');
    Route::delete('/souscategorie/{sousCategory}', [DescriptionController::class, 'destroySousCategorie'])->name('souscategorie.destroy');
});
