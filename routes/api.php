<?php

use App\Http\Controllers\Api\BankDetailController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartitemController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyAddressController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\RepController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\StripeWebhookController;

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
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Protected routes (requires valid JWT via 'auth.api' middleware)
Route::middleware(['auth.api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::delete('/user-account/delete', [AuthController::class, 'deleteUserAccount']);
    Route::put('/user-profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/send-referral', [AuthController::class, 'sendReferralEmail']);
    
    // Brands
    Route::get('/brands', [BrandController::class, 'index']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);

    // Sliders Banner
    Route::get('/home-banner', [BannerController::class, 'homeBanner']);
    Route::get('/products-banner', [BannerController::class, 'productBanners']);
    Route::get('/round-banner', [BannerController::class, 'roundBanner']);
    Route::get('/big-banner', [BannerController::class, 'largeBanner']);
    Route::get('/small-banner', [BannerController::class, 'smallBanner']);
    Route::get('/deals-banner', [BannerController::class, 'dealBanner']);
    Route::get('/fruit-banner', [BannerController::class, 'fruitBanner']);
    Route::get('/new-product-banner', [BannerController::class, 'newProductBanner']);
    Route::get('/top-seller-banner', [BannerController::class, 'topSellerBanner']);
    Route::get('/browse-banner', [BannerController::class, 'browseBanner']);
    Route::get('/loyalty-reward-banner', [BannerController::class, 'loyaltyRewardBanner']);

    // Wishlist
    Route::get  ('/wishlist',      [WishlistController::class, 'index']);
    Route::post ('/wishlist/add',[WishlistController::class, 'store']);

    // Cart Item
    Route::get('/cart-item', [CartitemController::class, 'index']);
    Route::post('/cart-item/update', [CartitemController::class, 'store']);

    // Company Address
    Route::get('/company-address', [CompanyAddressController::class, 'index']);
    Route::post('/company-address/upsert', [CompanyAddressController::class, 'upsertCompanyAddress']);
    Route::delete('/company-address/delete', [CompanyAddressController::class, 'deleteCompanyAddress']);


    // Delivery Methods
    Route::get('/delivery-methods', [CompanyAddressController::class, 'deliveryMethod']);

    // Service & Solution
    Route::get('/service-solutions', [CompanyAddressController::class, 'serviceAndSolution']);
    Route::post('/service-solutions', [CompanyAddressController::class, 'serviceInterested']);

    // Order Routes 
    Route::apiResource('orders', OrderController::class);

    // Coupon Routes 
    Route::get('/coupons',             [CouponController::class, 'index']); 
    Route::get('/coupons/{coupon_id}', [CouponController::class, 'show']); 

    // Cart coupon apply
    Route::post('/cart/apply-coupon', [CouponController::class, 'applyCoupon']);

    // check rep code 
    Route::get('reps/check/{code}', [RepController::class, 'checkRepCode']);

    // Setting Min order delivery
    Route::get('/settings/delivery-setting', [SettingController::class, 'getMinOrder']);

    Route::prefix('wallet')->group(function () {
        Route::get('/balance', [WalletController::class, 'balance']);
        Route::post('/credit', [WalletController::class, 'credit']);
        Route::post('/debit', [WalletController::class, 'debit']);
        Route::get('/transactions', [WalletController::class, 'transactions']);
    });

    // Bank Detail Routes 
    Route::get('/bank-detail',[BankDetailController::class, 'index']); 

    Route::post('/create-payment-intent', [StripeController::class, 'createPaymentIntent']);
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

});


