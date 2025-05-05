<?php

use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within the
| group which is assigned the "api" middleware group.
|
*/

// Example Sanctum route (only if needed):
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// -------------------------------
// JWT ROUTES
// -------------------------------

// Public route for login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (requires valid JWT via 'auth.api' middleware)
Route::middleware(['auth.api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);

    // Brands API 
    Route::get('/brands', [BrandController::class, 'index']);

    // Categories API 
    Route::get('/categories', [CategoryController::class, 'index']);

    // Banner API 
    Route::get('/browse-banner', [BannerController::class, 'browseBanner']);
});


