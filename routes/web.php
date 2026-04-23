<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientAuthController;  // ← manquait !
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ============================================================
// 🌐 PAGE D'ACCUEIL
// ============================================================
Route::get('/', [UserController::class, 'index'])->name('home');

// ============================================================
// 🔐 AUTH ADMIN — login/register/logout (fichier auth.php)
// ============================================================
require __DIR__.'/auth.php';

// Logout admin
Route::post('/logout', function () {
    Auth::guard('web')->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('client.dashboard');
})->middleware('auth')->name('logout');

// ============================================================
// 🌐 ROUTES PUBLIQUES CLIENT
// ============================================================
Route::get('/client/dashboard', [ClientController::class, 'index'])
    ->name('client.dashboard');

// ============================================================
// 🔐 AUTH CLIENT — Register / Login / Logout
// ============================================================
Route::post('/client/register', [ClientAuthController::class, 'register'])
    ->name('client.register');

Route::post('/client/login', [ClientAuthController::class, 'login'])
    ->name('client.login');

Route::post('/client/logout', [ClientAuthController::class, 'logout'])
    ->name('client.logout');

// ============================================================
// 🔒 ROUTES PROTÉGÉES CLIENT
// ============================================================
Route::middleware(['client.auth'])->group(function () {

    Route::get('/achat', [ClientController::class, 'achat'])
        ->name('client.achat');

    Route::post('/client/create', [ClientController::class, 'createNewClient'])
        ->name('client.createNewClient');

});
