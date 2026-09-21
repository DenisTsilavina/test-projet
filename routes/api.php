<?php

use App\Http\Controllers\CommandeController;
use App\Http\Controllers\StockControllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| CHANGEMENT : ce fichier est désormais la SEULE source des routes /api/*.
| Le groupe Route::prefix('api')->middleware('auth') qui existait en double
| dans web.php a été supprimé — il créait des routes dupliquées/en conflit
| avec celles-ci (même URI /api/commandes, middlewares différents).
| ClientApiController (doublon cassé de Api\ClientController) n'est plus
| utilisé — peut être supprimé du projet.
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Espace client connecté
Route::middleware('auth:sanctum')->prefix('client')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard']);
    Route::post('/logout', [ClientController::class, 'logout']);
});

// CHANGEMENT : StockControllers ajouté ici (existait dans le groupe
// dupliqué de web.php, absent de ce fichier jusque-là).
Route::middleware('auth:sanctum')->get('/stocks', [StockControllers::class, 'index']);

// Commandes du client (et vues admin sur les mêmes commandes)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/commandes', [CommandeController::class, 'index']);
    Route::post('/commandes', [CommandeController::class, 'store']);
    Route::get('/commandes/{commande}', [CommandeController::class, 'show']);
    Route::put('/commandes/{commande}', [CommandeController::class, 'update']);
    Route::post('/commandes/{commande}/approuver', [CommandeController::class, 'approuver']);
    Route::post('/commandes/{commande}/refuser', [CommandeController::class, 'refuser']);
    Route::delete('/commandes/{commande}', [CommandeController::class, 'destroy']);
});
