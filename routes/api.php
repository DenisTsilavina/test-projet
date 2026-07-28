<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Toutes les routes pour le client connecté
Route::middleware('auth:sanctum')->prefix('client')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard']);
    Route::post('/logout', [ClientController::class, 'logout']);
});
Route::get('/client/home', [UserController::class, 'homeData']);
