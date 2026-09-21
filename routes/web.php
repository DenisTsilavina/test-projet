<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Routes Publiques & Authentification Standard
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Formulaire / Inscription d'administration
|--------------------------------------------------------------------------
*/
Route::get('/admin/users/login', function () {
    return view('admin.users.login');
})->name('admin.users.login');

Route::post('/admin/users', [UserController::class, 'store'])->name('user.store');

/*
|--------------------------------------------------------------------------
| Routes Protégées Authentifiées
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::view('/profile', 'profile')->name('profile');

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [UserController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Espace Client (Géré par UserController)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:0'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [UserController::class, 'clientDashboard'])->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/
require __DIR__.'/admin.php';

/*
|--------------------------------------------------------------------------
| Route Fallback Vue.js / SPA (Toujours en dernier)
|--------------------------------------------------------------------------
*/
Route::get('/{any}', function () {
    return view('layouts.client');
})->where('any', '^(?!(api|admin)).*')->middleware('auth')->name('spa');
