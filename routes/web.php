<?php

use App\Http\Controllers\StockControllers; // Attention au "s" à StockControllers, vérifiez le nom de votre fichier
use App\Http\Controllers\DescriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// ─── Profil ───────────────────────────────────────────────────────────────────
Route::view('profile', 'profile')
    ->middleware('auth')
    ->name('profile');


require __DIR__.'/auth.php';
// APRÈS
// Logout
Route::post('/logout', [UserController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// CRUD Users (Super Admin seulement)
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

// ─── Redirection post-login (selon rôle) ─────────────────────────────────────
Route::get('/dashboard', [UserController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ─── Espace Client ────────────────────────────────────────────────────────────
// Note : Vérifiez si votre middleware accepte les slugs 'client' au lieu de '0' pour plus de clarté
Route::middleware(['auth', 'role:0'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [UserController::class, 'clientDashboard'])->name('dashboard');
    });

// ─── Espace Admin & Super Admin ───────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth') // Idéalement, appliquez un middleware global ici comme 'role:admin,super_admin'
    ->group(function () {

        // --- SECTION VENTES & DASHBOARD ---
        // Remplacement de 'admin.dashboard' par 'admin.vente.dashboard' pour s'aligner sur le layout
        Route::get('/dashboard', [UserController::class, 'adminDashboard'])->name('vente.dashboard');

        Route::get('/vente',            [VenteController::class, 'index'])->name('vente.index');
        Route::get('/vente/create',     [VenteController::class, 'create'])->name('vente.create');
        Route::post('/vente',           [VenteController::class, 'store'])->name('vente.store');
        Route::delete('/vente/{vente}', [VenteController::class, 'destroy'])->name('vente.destroy');

        // --- SECTION SUPER ADMIN (Ajouté pour compléter le layout) ---
        Route::middleware('role:super_admin')->group(function () {
            Route::get('/super/dashboard', function () {
                return view('admin.super.dashboard');
            })->name('super.dashboard'); // Crée la route admin.super.dashboard demandée par le layout
        });
    });

// ─── Gestion des Stocks (Indépendant ou partagé) ─────────────────────────────
Route::prefix('stock')
    ->middleware('auth')
    ->name('stock.')
    ->group(function () {

        // Routes statiques AVANT les routes dynamiques
        Route::get('/',              [StockControllers::class, 'index'])->name('index');
        Route::get('/inventaire',    [StockControllers::class, 'inventaire'])->name('inventaire');
        Route::get('/create',        [StockControllers::class, 'create'])->name('create');
        Route::post('/',             [StockControllers::class, 'store'])->name('store');

        // Descriptions (associées aux stocks)
        Route::get('/description/create/{stock_id}', [DescriptionController::class, 'createdescription'])
            ->name('description.create');
        Route::post('/description/store/{id_stock}', [DescriptionController::class, 'store'])
            ->name('description.store');

        // Routes dynamiques EN DERNIER (pour éviter les conflits avec /inventaire ou /create)
        Route::get('/{stock}',       [StockControllers::class, 'show'])->name('show');
        Route::get('/{stock}/edit',  [StockControllers::class, 'edit'])->name('edit');
        Route::put('/{stock}',       [StockControllers::class, 'update'])->name('update');
        Route::delete('/{stock}',    [StockControllers::class, 'destroy'])->name('destroy');
    });

// ─── Suppression description (hors préfixe stock) ────────────────────────────
Route::delete('/description/{description}', [DescriptionController::class, 'destroy'])
    ->middleware('auth')
    ->name('description.destroy');
