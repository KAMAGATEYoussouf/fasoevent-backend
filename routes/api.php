<?php

use App\Http\Controllers\Api\Private\Admin\CityController;
use App\Http\Controllers\Api\Private\Admin\EventController;
use App\Http\Controllers\Api\Private\User\EventUserController;
use App\Http\Controllers\Api\Public\AuthController;
use App\Http\Controllers\Api\Public\EventPublicController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/me', [AuthController::class, 'me']);




// Routes publiques pour les événements
Route::get('public/events', [EventPublicController::class, 'index'])->name('public.events.index');
Route::get('public/events/{id}', [EventPublicController::class, 'show'])->name('public.events.show');


// Routes pour la gestion des réservations des utilisateurs
Route::middleware('auth:sanctum')->group(function () {

    Route::middleware(['role:admin'])->group(function () {
        // Routes en resource pour les villes (CRUD complet : index, store, show, update, destroy)
        Route::apiResource('cities', CityController::class,);

        // Routes en resource pour les événements (CRUD complet : index, store, show, update, destroy)
        Route::apiResource('events', EventController::class);

        // Route pour le toggle du statut is_active (ex: POST /api/events/{id}/toggle)
        Route::post('events/{id}/toggle', [EventController::class, 'toggleActive']);
    });

    Route::get('user/events', [EventUserController::class, 'index'])->name('user.events.index');
    Route::post('user/events/{eventId}/reserve', [EventUserController::class, 'reserve'])->name('user.events.reserve');
    Route::delete('user/events/{eventId}/cancel', [EventUserController::class, 'cancel'])->name('user.events.cancel');
});
