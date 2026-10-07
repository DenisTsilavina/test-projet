<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientRegisterController;
use App\Enums\UserRole;

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

// Inscription publique
Route::middleware('guest')->group(function () {
    Route::get('/inscription',  [ClientRegisterController::class, 'show'])->name('client.register');
    Route::post('/inscription', [ClientRegisterController::class, 'store'])->name('client.register.store');
});

// Routes d'administration
require __DIR__.'/admin.php';

Route::post('/logout', [UserController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', [UserController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Espace Client (Vue.js SPA)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    // Vue hôte
    Route::get('/dashboard', [UserController::class, 'clientDashboard'])
        ->name('dashboard');

    // API pour Axios
    Route::get('/data', [UserController::class, 'clientData'])
        ->name('data');
});

/*
|--------------------------------------------------------------------------
| Fallback SPA Vue.js (TOUJOURS EN DERNIER)
|--------------------------------------------------------------------------
*/
Route::get('/{any}', function () {
    if (auth()->check() && auth()->user()->role !== UserRole::CLIENT) {
        return redirect()->route('admin.dashboard');
    }
    return view('layouts.client');
})->where('any', '.*')->middleware('auth');
