<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BannedPokemonController;
use App\Http\Controllers\Api\InfoController;
use App\Http\Controllers\Api\CustomPokemonController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Info route
Route::get('/info', [InfoController::class, 'index']);

// Protected routes
Route::middleware('auth.secret')->group(function () {
    // Banned routes
    Route::post('/banned', [BannedPokemonController::class, 'store']);
    Route::get('/banned', [BannedPokemonController::class, 'index']);
    Route::delete('/banned/{name}', [BannedPokemonController::class, 'destroy']);

    // Custom routes
    Route::post('/custom', [CustomPokemonController::class, 'store']);
    Route::get('/custom', [CustomPokemonController::class, 'index']);
    Route::put('/custom/{name}', [CustomPokemonController::class, 'update']);
    Route::delete('/custom/{name}', [CustomPokemonController::class, 'destroy']);
});