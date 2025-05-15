<?php

use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartitemController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyAddressController;
use App\Http\Controllers\Api\WishlistController;
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

    // Brands
    Route::get('/brands', [BrandController::class, 'index']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);

    // Browse Banner
    Route::get('/browse-banner', [BannerController::class, 'browseBanner']);

    // Wishlist
    Route::get  ('/wishlist',      [WishlistController::class, 'index']);
    Route::post ('/wishlist/add',[WishlistController::class, 'store']);

    // Cart Item
    Route::get('/cart-item', [CartitemController::class, 'index']);
    Route::post('/cart-item/update', [CartitemController::class, 'store']);

    // Company Address
    Route::get('/company-address', [CompanyAddressController::class, 'index']);
    Route::post('/company-address/update', [CompanyAddressController::class, 'store']);

});


