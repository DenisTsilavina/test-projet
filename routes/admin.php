<?php

use App\Http\Controllers\AchatController;
use App\Http\Controllers\ProduitController;
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
*/
Route::prefix('stock')
    ->middleware('auth')
    ->name('stock.')
    ->group(function () {

        Route::get('/',              [StockControllers::class, 'index'])->name('index');
        Route::get('/inventaire',    [StockControllers::class, 'inventaire'])->name('inventaire');

        Route::middleware('role:admin,super_admin')->group(function () {
            Route::get('/create',        [StockControllers::class, 'create'])->name('create');
            Route::post('/',             [StockControllers::class, 'store'])->name('store');
            Route::get('/{stock}/edit',  [StockControllers::class, 'edit'])->name('edit');
            Route::put('/{stock}',       [StockControllers::class, 'update'])->name('update');
            Route::delete('/{stock}',    [StockControllers::class, 'destroy'])->name('destroy');
        });

        Route::get('/{stock}',       [StockControllers::class, 'show'])->name('show');
    });

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

/*
|--------------------------------------------------------------------------
| Achats & Approvisionnements
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::resource('achat', AchatController::class);
    Route::post('achat/{achat}/valider', [AchatController::class, 'valider'])->name('achat.valider');
});

/*
|--------------------------------------------------------------------------
| Produits (stock / fini / compositions)
|--------------------------------------------------------------------------
| AJOUT : manquait entièrement.
*/
Route::middleware('auth')->group(function () {
    Route::resource('produit', ProduitController::class);
});
