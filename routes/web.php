<?php

use App\Http\Controllers\SendcloudSyncController;
use App\Http\Controllers\BankDetailController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserTagController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\McategoryController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomebannerController;
use App\Http\Controllers\RepController;

require __DIR__.'/customer.php';

// Auth
Auth::routes();

// Public welcome page
Route::get('/', function () {
    return view('welcome');
});

// Admin Auth Pages (Login)
Route::prefix('admin')->middleware('admin.guest')->group(function () {
    Route::view('/login', 'admin.login')->name('admin.login');
    Route::post('/login', [AdminController::class, 'adminlogin'])->name('adminlogin.submit');
});

// Admin Protected API Routes
Route::prefix('admin')->middleware('admin.auth')->group(function () {

    // Basic Logout
    Route::get('/logout', [AdminController::class, 'adminlogout'])->name('admin.logout');

    // Admin Dashboard routes
    Route::get('/dashboard/vlist', [DashboardController::class, 'index']);

    // Customers User routes
    Route::get('/users/vlist', [AdminController::class, 'userVlist'])->name('users.vlist');
    Route::post('/users/update-approval', [AdminController::class, 'updateUserApproval']);
    Route::put('/users/{user}', [AdminController::class, 'updateUserProfile']);
    Route::post('/check-email', [AdminController::class, 'checkEmail']);
    Route::post('/users/update-wallet', [AdminController::class, 'updateUserWallet']);

    // Options routes
    Route::get('/moptions/vlist', [AdminController::class, 'moptionsVlist'])->name('moptions.vlist');
    Route::post('/moption/add', [AdminController::class, 'addMoption'])->name('moption.add');
    Route::post('/moption/update', [AdminController::class, 'editMoption'])->name('moption.edit');
    Route::post('/moption-delete', [AdminController::class, 'deleteMoption']);

    // Brands routes
    Route::get('/mbrands/vlist', [AdminController::class, 'mbrandVlist'])->name('mbrands.vlist');
    Route::post('/mbrands/add', [AdminController::class, 'addBrand'])->name('mbrand.add');
    Route::post('/mbrands/update', [AdminController::class, 'editBrand'])->name('mbrand.edit');
    Route::post('/mbrand-delete', [AdminController::class, 'deleteBrand']);
    Route::post('/mbrands/bulk-delete', [AdminController::class, 'bulkDeleteMbrand']);

    // Tags routes
    Route::get('/mtags/vlist', [AdminController::class, 'mtagVlist'])->name('mtags.vlist');

    // Products routes
    Route::get('/products/vlist', [AdminController::class, 'adminProductlist'])->name('products.vlist');
    Route::get('/product/pdata', [AdminController::class, 'productAddData'])->name('adminproduct.pdata');
    Route::post('/product-duplicate', [AdminController::class, 'productDuplicate'])->name('mproduct.duplicate');
    Route::post('/product-delete', [AdminController::class, 'deleteProduct']);
    Route::post('/products/bulk-delete', [AdminController::class, 'bulkDeleteProduct']);
    Route::post('/save-product', [AdminController::class, 'productStoreData'])->name('adminproduct.storedata');
    Route::post('/update-product', [AdminController::class, 'updateProductData'])->name('adminproduct.update-product');
    Route::get('/vproduct/editdata', [AdminController::class, 'productEditData'])->name('adminproduct.editdata');

    // Products more bulk option routes
    Route::post('/products-bulk/mark-status', [AdminController::class, 'productsBulkmarkStatus']);
    Route::post('/products-bulk/delete', [AdminController::class, 'productsBulkDelete']);
    Route::post('/products-bulk/add-tags', [AdminController::class, 'productsBulkAddTags']);
    Route::post('/products-bulk/remove-tags', [AdminController::class, 'productsBulkRemoveTags']);
    // Product add view add type, brand, tag
    Route::post('/mproduct-types', [AdminController::class, 'storeProductType']);
    Route::post('/mbrands', [AdminController::class, 'storeBrand']);
    Route::post('/mtags', [AdminController::class, 'storeTag']);

    // Product Offers
    Route::get('/product-offers/vlist', [AdminController::class, 'productofferVlist'])->name('productoffers.vlist');
    Route::post('/product-offers/add', [AdminController::class, 'addProductoffer'])->name('productoffer.add');
    Route::post('/product-offers/update', [AdminController::class, 'editProductoffer'])->name('productoffer.edit');
    Route::post('/product-offers/delete', [AdminController::class, 'deleteProductoffer'])->name('productoffer.delete');
    Route::post('/product-offers/bulk-delete', [AdminController::class, 'bulkDeleteProductoffer']);
    Route::post('/product-offers/bulk-add', [AdminController::class, 'bulkAddProductoffer']);
    Route::post('/product-offers/bulk-remove', [AdminController::class, 'bulkRemoveProductoffer']);

    // Main Categories routes
    Route::get('/main-mcategories/vlist', [McategoryController::class, 'mainMcatVlist'])->name('mainmcats.vlist');
    Route::post('/main-mcategory/add', [McategoryController::class, 'addMainMcat'])->name('mainmcat.add');
    Route::post('/main-mcategory/update', [McategoryController::class, 'editMainMcat'])->name('mainmcat.edit');
    Route::post('/main-mcategory-delete', [McategoryController::class, 'deleteMainMcat']);
    Route::post('/main-mcategories/reorder', [McategoryController::class, 'mainCatReorder']);
    Route::post('/main-mcategories/status-toggle/{id}', [McategoryController::class, 'mainCatToggleStatus']);
    Route::get('/maincategories/vlist', [McategoryController::class, 'vlist']); 

    // Categories routes
    Route::get('/mcategories/vlist', [McategoryController::class, 'mcatVlist'])->name('mcats.vlist');
    Route::post('/mcategory/add', [McategoryController::class, 'addMcat'])->name('mcat.add');
    Route::post('/mcategory/update', [McategoryController::class, 'editMcat'])->name('mcat.edit');
    Route::post('/mcategory-delete', [McategoryController::class, 'deleteMcat']);
    Route::post('/mcategories/bulk-delete', [McategoryController::class, 'bulkDeleteMcat']);
    Route::post('/mcategories/status-toggle/{id}', [McategoryController::class, 'mcatToggleStatus']);

    // Sub-Categories routes
    Route::get('/msub-categories/vlist', [McategoryController::class, 'mcatsubVlist'])->name('msubcats.vlist');
    Route::get('/vsub-category/editdata/{msubcatid}', [McategoryController::class, 'msubcatEditData'])->name('msubcat.editdata');
    Route::post('/msub-category/add', [McategoryController::class, 'addMsubcat'])->name('msubcat.add');
    Route::post('/msub-category/{msubcatid}/update', [McategoryController::class, 'updateMsubcatData'])->name('msubcat.update');
    Route::post('/msub-category/status-toggle/{id}', [McategoryController::class, 'msubcatToggleStatus']);

    Route::post('/msub-category-delete', [McategoryController::class, 'deleteMsubcat']);
    Route::post('/msub-categories/bulk-delete', [McategoryController::class, 'bulkDeleteMsubcat']);

    // Sub-Categories Collection API routes
    Route::get('/mcollproducts/vlist', [McategoryController::class,'productsVlist'])->name('mcollproducts.vlist');
    Route::get('/querys/vlist', [McategoryController::class,'querysVlist'])->name('querys.vlist');
    
    // Browse sliders routes
    Route::get('/browsebanners/vlist', [BannerController::class, 'browseBannerVlist'])->name('browsebanners.vlist');
    Route::post('/browsebanners/add', [BannerController::class, 'addBrowseBanner'])->name('browsebanner.add');
    Route::post('/browsebanners/update', [BannerController::class, 'editBrowseBanner'])->name('browsebanner.edit');
    Route::post('/browsebanners/reorder', [BannerController::class, 'reorder']);
    Route::post('/browsebanner-delete', [BannerController::class, 'deleteBrowseBanner'])->name('browsebanner.delete');

    // Loyalty rewards sliders routes
    Route::get('/loyalty-rewards/vlist', [BannerController::class, 'loyaltyRewardVlist'])->name('loyaltyrewards.vlist');
    Route::post('/loyalty-rewards/add', [BannerController::class, 'addLoyaltyReward'])->name('loyaltyrewards.add');
    Route::post('/loyalty-rewards/update', [BannerController::class, 'editLoyaltyReward'])->name('loyaltyrewards.edit');

    // Home Round sliders routes
    Route::get('/round-banners/vlist',[HomebannerController::class,'roundBannerVlist'])->name('roundbanners.vlist');
    Route::post('/round-banners/add',[HomebannerController::class,'addRoundBanner'])->name('roundbanners.add');
    Route::post('/round-banners/update',[HomebannerController::class,'editRoundBanner'])->name('roundbanner.edit');
    Route::post('/round-banners/reorder', [HomebannerController::class, 'roundreorder']);
    Route::post('/round-banners-delete', [HomebannerController::class, 'deleteRoundBanner'])->name('roundbanner.delete');

    // Home Large sliders routes
    Route::get('/large-banners/vlist',[HomebannerController::class,'largeBannerVlist'])->name('largebanners.vlist');
    Route::post('/large-banners/add',[HomebannerController::class,'addLargeBanner'])->name('largebanner.add');
    Route::post('/large-banners/update',[HomebannerController::class,'editLargeBanner'])->name('largebanner.edit');
    Route::post('/large-banners/reorder', [HomebannerController::class, 'largereorder']);
    Route::post('/large-banners-delete', [HomebannerController::class, 'deleteLargeBanner'])->name('largebanner.delete');

    // Home Small sliders routes
    Route::get('/small-banners/vlist',[HomebannerController::class,'smallBannerVlist'])->name('smallbanners.vlist');
    Route::post('/small-banners/add',[HomebannerController::class,'addSmallBanner'])->name('smallbanner.add');
    Route::post('/small-banners/update',[HomebannerController::class,'editSmallBanner'])->name('smallbanner.edit');
    Route::post('/small-banners/reorder', [HomebannerController::class, 'smallreorder']);
    Route::post('/small-banners-delete', [HomebannerController::class, 'deleteSmallBanner'])->name('smallbanner.delete');

    // Home Explore sliders routes
    Route::get('/explore-deal-banners/vlist',[HomebannerController::class,'exploreDealBannerVlist'])->name('exploredealbanners.vlist');
    Route::post('/explore-deal-banners/add',[HomebannerController::class,'addExploreDealBanner'])->name('exploredealbanner.add');
    Route::post('/explore-deal-banners/update',[HomebannerController::class,'editExploreDealBanner'])->name('exploredealbanner.edit');
    Route::post('/explore-deal-banners/reorder', [HomebannerController::class, 'exploreDealreorder']);
    Route::post('/explore-deal-banners-delete', [HomebannerController::class, 'deleteExploreDealBanner'])->name('exploredealbanner.delete');

    // Home Fruit sliders routes
    Route::get('/fruit-banners/vlist',[HomebannerController::class,'fruitBannerVlist'])->name('fruitbanners.vlist');
    Route::post('/fruit-banners/add',[HomebannerController::class,'addFruitBanner'])->name('fruitbanner.add');
    Route::post('/fruit-banners/update',[HomebannerController::class,'editFruitBanner'])->name('fruitbanner.edit');
    Route::post('/fruit-banners/reorder', [HomebannerController::class, 'fruitreorder']);
    Route::post('/fruit-banners-delete', [HomebannerController::class, 'deleteFruitBanner'])->name('fruitbanner.delete');

    // product variants routes using in new & top sliders
    Route::get('/variants/list',             [HomebannerController::class,'variantVlist']);

    // Home New Product sliders routes
    Route::get ('/new-products',             [HomebannerController::class,'newProductVlist']);
    Route::post('/new-products',             [HomebannerController::class,'addNewProduct']);
    Route::delete('/new-products/{id}',      [HomebannerController::class,'deleteNewProduct']);
    Route::post('/new-products/bulk-delete', [HomebannerController::class,'bulkDeleteNewProduct']);

    // Home Top Seller sliders routes
    Route::get ('/top-sellers',             [HomebannerController::class,'topSellerVlist']);
    Route::post('/top-sellers',             [HomebannerController::class,'addTopSeller']);
    Route::delete('/top-sellers/{id}',      [HomebannerController::class,'deleteTopSeller']);
    Route::post('/top-sellers/bulk-delete', [HomebannerController::class,'bulkDeleteTopSeller']);

    // Home Slider Headers routes
    Route::get('/slider-headers/vlist', [HomebannerController::class, 'sliderHeaderVlist']);
    Route::post('/slider-headers/update', [HomebannerController::class, 'editSliderHeader']);

    // Main Category api categories->sub-categories->products
    Route::get('/main/categories', [BannerController::class, 'index']);


    // Rep Management Routes
    Route::get('/reps/vlist', [RepController::class, 'repVlist'])->name('reps.vlist'); 
    Route::post('/reps/store', [RepController::class, 'addRep'])->name('rep.add');   
    Route::post('/reps/{id}/update', [RepController::class, 'editRep'])->name('rep.edit'); 
    Route::post('/rep-delete', [RepController::class, 'deleteRep'])->name('reps.delete');
    Route::post('/users/assign-rep', [RepController::class, 'assignRep']);  

    // Setting Min order delivery
    Route::get('/settings/min-order/vlist', [SettingController::class, 'getMinOrder']);
    Route::post('/settings/min-order', [SettingController::class, 'saveMinOrder']);
    Route::post('/settings/toggle/{setting}', [SettingController::class, 'toggle']);

    // Setting Min order place
    Route::get('/settings/min-order-place/vlist', [SettingController::class, 'getMinOrderPlace']);
    Route::post('/settings/min-order-place', [SettingController::class, 'saveMinOrderPlace']);
    Route::post('/settings/toggle/min-order-place/{setting}', [SettingController::class, 'toggleMinOrderPlace']);

    // Bank Details routes
    Route::get('/bank-detail/vlist', [BankDetailController::class, 'index'])->name('bankdetails.vlist');
    Route::post('/bank-detail/add', [BankDetailController::class, 'addBankDetail'])->name('bankdetail.add');
    Route::post('/bank-detail/update', [BankDetailController::class, 'editBankDetail'])->name('bankdetail.edit');
    Route::post('/bank-detail-delete', [BankDetailController::class, 'deleteBankDetail']);
    Route::post('/bank-detail/status-toggle/{id}', [BankDetailController::class, 'bankDetailToggleStatus']);

    // Sendcloud Integration routes
    Route::get('/sendcloud-integration/vlist', [BankDetailController::class, 'sendcloudVlist']);
    Route::post('/sendcloud-integration/add', [BankDetailController::class, 'addSendcloud']);
    Route::post('/sendcloud-integration/update', [BankDetailController::class, 'editSendcloud']);
    Route::post('/sendcloud-integration/status-toggle/{id}', [BankDetailController::class, 'sendcloudToggleStatus']);

    // Stripe Integration routes
    Route::get('/stripe-integration/vlist', [BankDetailController::class, 'stripeVlist']);
    Route::post('/stripe-integration/update', [BankDetailController::class, 'editStripe']);
    Route::post('/stripe-integration/status-toggle/{id}', [BankDetailController::class, 'stripeToggleStatus']);

    // Coupons routes
    Route::get('/coupons/vlist', [CouponController::class, 'index'])->name('coupons.vlist');
    Route::post('/coupons/add', [CouponController::class, 'addCoupon'])->name('coupon.add');
    Route::post('/coupons/update', [CouponController::class, 'editCoupon'])->name('coupon.edit');
    Route::post('/coupon-delete', [CouponController::class, 'deleteCoupon']);
    Route::post('/coupons/bulk-delete', [CouponController::class, 'bulkDeleteCoupon']);
    Route::post('/coupons/status-toggle/{id}', [CouponController::class, 'couponToggleStatus']);

    // Orders routes
    Route::get('/orders/vlist', [OrderController::class, 'index'])->name('orders.vlist');
    Route::post('/orders/update-status', [OrderController::class, 'updateOrderStatus']);
    Route::get('/order-receipts/show/{order_id}', [OrderController::class, 'showByOrderId']);
    Route::post('/orders-bulk/mark-status', [OrderController::class, 'ordersBulkMarkStatus']);
    Route::post('/orders-bulk/mark-fulfillment', [OrderController::class, 'ordersBulkMarkFulfilled']);
    Route::get('/vorder/editdata/{orderid}', [OrderController::class, 'orderEditData']);
    Route::post('/orders/mark-as-paid', [OrderController::class, 'markAsPaid']);
    Route::post('/orders/fulfill', [OrderController::class, 'fulfill']);
    Route::post('/orders/fulfillments/add-tracking', [OrderController::class, 'addTracking']);
    Route::post('/order/send-invoice', [OrderController::class, 'sendInvoice']);
    Route::post('/order/cancel', [OrderController::class, 'cancelOrder']);
    Route::get('/order/packing-slip/{order_id}', [OrderController::class, 'packingSlip'])->name('admin.order.packingSlip');
    Route::get('/orders-bulk/packing-slips', [OrderController::class, 'bulkPackingSlips'])->name('admin.order.bulkpackingSlip');
    Route::get('/orders/count', [OrderController::class, 'countOrders']);

    // routes/ Click & drop royal mail
    Route::post('/sendcloud/sync', [SendcloudSyncController::class, 'syncSendcloud']);


    // Delivery Methods routes
    Route::get('/delivery-method/vlist', [SettingController::class, 'deliveryMethodVlist'])->name('deliverymethods.vlist');
    Route::post('/delivery-method/add', [SettingController::class, 'addDeliveryMethod'])->name('deliverymethod.add');
    Route::post('/delivery-method/update', [SettingController::class, 'editDeliveryMethod'])->name('deliverymethod.edit');
    Route::post('/delivery-method-delete', [SettingController::class, 'deleteDeliveryMethod']);
    Route::post('/delivery-method/status-toggle/{id}', [SettingController::class, 'deliveryMethodToggleStatus']);

    // Product vat routes
    Route::get('/product-vat/vlist', [SettingController::class, 'productVatVlist']);
    Route::post('/product-vat/update', [SettingController::class, 'editProductVat']);

    // User Tag routes
    Route::get('/user-tags/vlist', [UserTagController::class, 'userTagVlist']);
    Route::post('/user-tag/add', [UserTagController::class, 'addUserTag']);
    Route::post('/user-tag/update', [UserTagController::class, 'editUserTag']);
    Route::post('/user-tag/delete', [UserTagController::class, 'deleteUserTag']);
    Route::post('/user-tag/status-toggle/{id}', [UserTagController::class, 'userTagToggleStatus']);
    Route::get('/user-tag-price/list',[UserTagController::class,'variantForTagPrice']);
    Route::post('/user-tag-price/update',[UserTagController::class,'updateTagPrice']);
    Route::post('/users/assign-tag', [UserTagController::class, 'assignTag']);  
    
    // Services & Display Solutions routes
    Route::get('/services/vlist', [CouponController::class, 'serviceVlist'])->name('services.vlist');
    Route::post('/services/add', [CouponController::class, 'addService'])->name('service.add');
    Route::post('/services/update', [CouponController::class, 'editService'])->name('service.edit');
    Route::post('/service-delete', [CouponController::class, 'deleteService']);
    Route::post('/services/bulk-delete', [CouponController::class, 'bulkDeleteService']);
});


// Home Routes
Route::get('/home', [HomeController::class, 'index'])->name('home');

// User Routes
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');

// Vue SPA Catch-All Route for admin (authenticated)
Route::get('/admin/{any}', function () {
    return view('spa');
})->where('any', '.*')->middleware('admin.auth');

// Vue SPA for all other
Route::get('/{any}', function () {
    return view('spa');
})->where('any', '^(?!admin).*$');
