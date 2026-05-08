<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientAuthController;
use Illuminate\Support\Facades\Route;

// ============================================================
// 🌐 PAGE D'ACCUEIL
// ============================================================
Route::get('/', [UserController::class, 'index'])->name('home');

// ============================================================
// 🌐 DASHBOARD CLIENT (public)
// ============================================================
Route::get('/client/dashboard', [ClientController::class, 'index'])
    ->name('client.dashboard');

// ============================================================
// 🔐 AUTH CLIENT
// ============================================================
Route::get('/client/login', function () {
    return redirect()->route('client.dashboard');
})->name('client.login');

Route::post('/client/login', [ClientAuthController::class, 'login'])
    ->name('client.login.submit');

Route::get('/client/register', function () {
    return redirect()->route('client.dashboard')
        ->with('show_register_modal', true);
})->name('client.register.form');

Route::post('/client/register', [ClientAuthController::class, 'register'])
    ->name('client.register');

Route::post('/client/logout', [ClientAuthController::class, 'logout'])
    ->name('client.logout');

// Vérification email
Route::get('/client/verify', [ClientAuthController::class, 'showVerifyForm'])
    ->name('client.verify.form');
Route::post('/client/verify', [ClientAuthController::class, 'verify'])
    ->name('client.verify');

// ============================================================
// 🔒 ROUTES PROTÉGÉES CLIENT
// ============================================================
Route::middleware(['client.auth'])->group(function () {
    Route::get('/client/achat', [ClientController::class, 'achat'])
        ->name('client.achat');

    Route::post('/client/vente', [ClientController::class, 'store'])
        ->name('client.vente.store');

    Route::post('/client/create', [ClientController::class, 'createNewClient'])
        ->name('client.createNewClient');

    Route::get('/commande/lance', [ClientController::class, 'create'])
        ->name('client.create');

    Route::post('/commande/create', [ClientController::class, 'lanceCommande'])
        ->name('client.lanceCommande');
});
Route::prefix('client')->name('client.')->middleware(['auth:client'])->group(function () {
    Route::get('vente/{vente}/recu', [ClientController::class, 'recu'])->name('vente.recu');
    // ... autres routes client
});

// ============================================================
// 🔐 AUTH ADMIN (Laravel UI)
// ============================================================
require __DIR__.'/admin.php';
