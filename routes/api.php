<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannedController;
use App\Http\Controllers\Api\InfoController;
use App\Http\Controllers\Api\CustomController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Info route
Route::get('/info', [InfoController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Banned routes
    Route::post('/banned', [BannedController::class, 'store']);
    Route::get('/banned', [BannedController::class, 'show']);
    Route::delete('/banned/{name}', [BannedController::class, 'remove']);

    // Custom routes
    Route::post('/custom', [CustomController::class, 'store']);
    Route::get('/custom', [CustomController::class, 'show']);
    Route::put('/custom/{name}', [CustomController::class, 'update']);
    Route::delete('/custom/{name}', [CustomController::class, 'remove']);
});