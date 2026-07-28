<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockControllers; // Vérifiez si votre contrôleur a bien un "s" à la fin
use App\Http\Controllers\DescriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CommandeAdminController;

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

// Déconnexion
Route::post('/logout', [UserController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Redirection post-login selon rôle
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

// Commandes Côté Client
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
| Espace Administration & Super Admin
|--------------------------------------------------------------------------
*/

// CRUD Utilisateurs (Super Admin seulement)
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

// Espace Admin Général
Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {

        // Ventes & Dashboard Admin
        Route::get('/dashboard', [UserController::class, 'adminDashboard'])->name('vente.dashboard');
        Route::get('/vente',            [VenteController::class, 'index'])->name('vente.index');
        Route::get('/vente/create',     [VenteController::class, 'create'])->name('vente.create');
        Route::post('/vente',           [VenteController::class, 'store'])->name('vente.store');
        Route::delete('/vente/{vente}', [VenteController::class, 'destroy'])->name('vente.destroy');

        // Gestion des Commandes côté Admin
        Route::get('/commandes', [CommandeAdminController::class, 'index'])->name('commandes.index');
        Route::get('/commandes/{commande}', [CommandeAdminController::class, 'show'])->name('commandes.show');
        Route::patch('/commandes/{commande}/demander-infos', [CommandeAdminController::class, 'demanderInfos'])->name('commandes.demander-infos');
        Route::patch('/commandes/{commande}/confirmer', [CommandeAdminController::class, 'confirmer'])->name('commandes.confirmer');
        Route::patch('/commandes/{commande}/refuser', [CommandeAdminController::class, 'refuser'])->name('commandes.refuser');

        // Dashboard Super Admin
        Route::middleware('role:super_admin')->group(function () {
            Route::get('/super/dashboard', function () {
                return view('admin.super.dashboard');
            })->name('super.dashboard');
        });
    });


/*
|--------------------------------------------------------------------------
| Gestion des Stocks & Sub-catégories
|--------------------------------------------------------------------------
*/
Route::prefix('stock')
    ->middleware('auth')
    ->name('stock.')
    ->group(function () {
        Route::get('/',              [StockControllers::class, 'index'])->name('index');
        Route::get('/inventaire',    [StockControllers::class, 'inventaire'])->name('inventaire');
        Route::get('/create',        [StockControllers::class, 'create'])->name('create');
        Route::post('/',             [StockControllers::class, 'store'])->name('store');

        // Descriptions liées au stock
        Route::get('/description/create/{stock_id}', [DescriptionController::class, 'createdescription'])->name('description.create');
        Route::post('/description/store/{id_stock}', [DescriptionController::class, 'store'])->name('description.store');

        // Paramètres dynamiques (en dernier)
        Route::get('/{stock}',       [StockControllers::class, 'show'])->name('show');
        Route::get('/{stock}/edit',  [StockControllers::class, 'edit'])->name('edit');
        Route::put('/{stock}',       [StockControllers::class, 'update'])->name('update');
        Route::delete('/{stock}',    [StockControllers::class, 'destroy'])->name('destroy');
    });

// Descriptions hors-préfixe stock
Route::middleware('auth')->group(function () {
    Route::get('/description/{description}/edit', [DescriptionController::class, 'edit'])->name('description.edit');
    Route::put('/description/{description}', [DescriptionController::class, 'update'])->name('description.update');
    Route::delete('/description/{description}', [DescriptionController::class, 'destroy'])->name('description.destroy');

    // Sous-catégories
    Route::get('/souscategorie/create/{description_id}', [DescriptionController::class, 'createSousCategorie'])->name('souscategorie.create');
    Route::post('/souscategorie', [DescriptionController::class, 'storeSousCategorie'])->name('souscategorie.store');
    Route::get('/souscategorie/{sousCategory}/edit', [DescriptionController::class, 'editSousCategorie'])->name('souscategorie.edit');
    Route::put('/souscategorie/{sousCategory}', [DescriptionController::class, 'updateSousCategorie'])->name('souscategorie.update');
    Route::delete('/souscategorie/{sousCategory}', [DescriptionController::class, 'destroySousCategorie'])->name('souscategorie.destroy');
});


/*
|--------------------------------------------------------------------------
| Route Fallback Vue.js / Single Page Application (À METTRE TOUT À LA FIN)
|--------------------------------------------------------------------------
*/
Route::get('/{any}', function () {
    return view('app'); // Assurez-vous que votre fichier blade s'appelle app.blade.php ou changez pour 'client.dashboard'
})->where('any', '.*')->middleware('auth');
