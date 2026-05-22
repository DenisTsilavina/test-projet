<?php

use App\Http\Controllers\Admin\CommandeAdminController;
use App\Http\Controllers\StockControllers;
use App\Http\Controllers\DescriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============================================================
// 🔐 AUTH ADMIN (Laravel UI)
// ✅ Auth::routes() génère automatiquement Route::get('/home')->name('home')
// ============================================================
Auth::routes();

// ============================================================
// 🔒 ROUTES PROTÉGÉES ADMIN / USER
// ============================================================
Route::middleware(['auth'])->group(function () {

    // ✅ Dashboard admin — écrase le /home généré par Auth::routes()
    Route::get('/dashboard', [UserController::class, 'index'])
        ->name('dashboard');

    // ========================
    //         Stocks
    // ========================
    Route::prefix('stock')->group(function () {
        Route::get('/stocks', [StockControllers::class, 'index'])
            ->name('stock.index');
        Route::get('/stocks/create', [StockControllers::class, 'create'])
            ->name('stock.create');
        Route::post('/stocks', [StockControllers::class, 'createStock'])
            ->name('stock.store');
        Route::post('/unite', [StockControllers::class, 'createUnite'])
            ->name('stock.createUnite');
        Route::post('/stock/description-complete/store/{id_stock}',
            [DescriptionController::class, 'store'])
            ->name('description.store');
        Route::get('/stock/create/description/{stock_id}',
            [DescriptionController::class, 'createdescription'])
            ->name('description.create');
        Route::delete('/description/{description}',
            [DescriptionController::class, 'destroy'])
            ->name('description.destroy');
        Route::get('/description/{id}/edit',
            [DescriptionController::class, 'descriptionUpdate'])
            ->name('description.edit');
        Route::put('/description/{id}/update',
            [DescriptionController::class, 'update'])
            ->name('description.update');
        Route::get('/admin/article/create', [UserController::class, 'showCreateForm'])->name('admin.article.create');

        // Route POST pour traiter l'envoi du formulaire (celle utilisée dans le <form action="...">)
        Route::post('/admin/article/store', [UserController::class, 'createArticles'])->name('admin.article.store');
    });

    // ========================
    //         Vente
    // ========================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/vente/dashboard', [VenteController::class, 'dashboard'])
            ->name('vente.dashboard');
        Route::get('/vente', [VenteController::class, 'index'])
            ->name('vente.index');
        Route::get('/vente/create', [VenteController::class, 'create'])
            ->name('vente.create');
        Route::post('/vente', [ClientController::class, 'store'])
            ->name('vente.store');
        Route::delete('/vente/{vente}', [VenteController::class, 'destroy'])
            ->name('vente.destroy');
       // Route::get('/vente/recu/{vente}', [VenteController::class, 'recu'])
        //    ->name('vente.recu');
    });
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
        Route::get('vente/{vente}/recu', [VenteController::class, 'recu'])->name('vente.recu');
        // ... autres routes admin
    });

    // ========================
    //    Commandes (admin only)
    // ========================
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/commandes', [CommandeAdminController::class, 'index'])
            ->name('commandes.index');
        Route::patch('/commandes/{commande}/status',
            [CommandeAdminController::class, 'updateStatus'])
            ->name('commandes.updateStatus');
    });
});
