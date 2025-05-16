<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\HomebannerController;
use App\Http\Controllers\McategoryController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

// Admin Routes
Route::group(['prefix'=> 'admin'], function (){
    Route::group(['middleware'=> 'admin.guest'],function(){
        Route::view('/login', 'admin.login')->name('admin.login');
        Route::post('/login', [AdminController::class, 'adminlogin'])->name('adminlogin.submit');
    });
    Route::group(['middleware'=>'admin.auth'], function(){
        Route::get('/logout', [AdminController::class, 'adminlogout'])->name('admin.logout');
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.index');

        // User routes
        Route::get('/users/list',[AdminController::class,'userList'])->name('users.list');
        Route::get('/users/vlist',[AdminController::class,'userVlist'])->name('users.vlist');
        Route::post('/users/update-approval', [AdminController::class, 'updateUserApproval']);

        // Options routes
        Route::get('/moptions/list',[AdminController::class,'moptionsList'])->name('moptions.list');
        Route::get('/moptions/vlist',[AdminController::class,'moptionsVlist'])->name('moptions.vlist');
        Route::post('/moption/add',[AdminController::class,'addMoption'])->name('moption.add');
        Route::post('/moption/update',[AdminController::class,'editMoption'])->name('moption.edit');
        Route::post('/moption-delete', [AdminController::class, 'deleteMoption']);

        // Brands routes
        Route::get('/mbrands/list',[AdminController::class,'mbrandList'])->name('mbrands.list');
        Route::get('/mbrands/vlist',[AdminController::class,'mbrandVlist'])->name('mbrands.vlist');
        Route::post('/mbrands/add',[AdminController::class,'addBrand'])->name('mbrand.add');
        Route::post('/mbrands/update',[AdminController::class,'editBrand'])->name('mbrand.edit');
        Route::post('/mbrand-delete', [AdminController::class, 'deleteBrand']);

        // Products routes
        Route::get('/products/list',[AdminController::class,'productsList'])->name('products.list');
        Route::get('/products/vlist',[AdminController::class,'adminProductlist'])->name('products.vlist');
        Route::get('/product/addview',[AdminController::class,'productAddView'])->name('adminproduct.addview');
        Route::get('/product/pdata',[AdminController::class,'productAddData'])->name('adminproduct.pdata');
        Route::post('/product-duplicate', [AdminController::class, 'productDuplicate'])->name('mproduct.duplicate');
        Route::post('/product-delete', [AdminController::class, 'deleteProduct']);
        
        // Product Variations routes
        Route::post('/save-product', [AdminController::class, 'productStoreData'])->name('adminproduct.storedata');
        Route::post('/mproduct-types', [AdminController::class, 'storeProductType']);
        Route::post('/mbrands', [AdminController::class, 'storeBrand']);
        Route::post('/mtags', [AdminController::class, 'storeTag']);
        Route::get('/product/{mproduct_id}',[AdminController::class,'adminProductEdit'])->name('adminproduct.edit');
        Route::get('/vproduct/editdata',[AdminController::class,'productEditData'])->name('adminproduct.editdata');
        Route::post('/update-product', [AdminController::class, 'updateProductData'])->name('adminproduct.update-product');

        // Product offers
        Route::get('/product-offers/list',[AdminController::class,'productofferlist'])->name('productoffers.list');
        Route::get('/product-offers/vlist',[AdminController::class,'productofferVlist'])->name('productoffers.vlist');
        Route::post('/product-offers/add',[AdminController::class,'addProductoffer'])->name('productoffer.add');
        Route::post('/product-offers/update',[AdminController::class,'editProductoffer'])->name('productoffer.edit');
        Route::post('/product-offers/delete',[AdminController::class,'deleteProductoffer'])->name('productoffer.delete');

        // Categories routes
        Route::get('/mcategories/list',[McategoryController::class,'index'])->name('mcats.list');
        Route::get('/mcategories/vlist',[McategoryController::class,'mcatVlist'])->name('mcats.vlist');
        Route::post('/mcategory/add',[McategoryController::class,'addMcat'])->name('mcat.add');
        Route::post('/mcategory/update',[McategoryController::class,'editMcat'])->name('mcat.edit');
        Route::post('/mcategory-delete', [McategoryController::class, 'deleteMcat']);

        // Sub-Categories routes
        Route::get('/msub-categories/list',[McategoryController::class,'mcatsubList'])->name('msubcats.list');
        Route::get('/msub-categories/vlist',[McategoryController::class,'mcatsubVlist'])->name('msubcats.vlist');
        Route::post('/msub-category/add',[McategoryController::class,'addMsubcat'])->name('msubcat.add');
        Route::get('/msub-category/add',[McategoryController::class,'addViewMsubcat'])->name('mcoll.add');
        Route::post('/msub-category-delete', [McategoryController::class, 'deleteMsubcat']);

        Route::get('/msub-category/{msubcatid}',[McategoryController::class,'msubcatEdit'])->name('msubcat.edit');
        Route::get('/vsub-category/editdata/{msubcatid}',[McategoryController::class,'msubcatEditData'])->name('msubcat.editdata');
        Route::post('/msub-category/{msubcatid}/update', [McategoryController::class, 'updateMsubcatData'])->name('msubcat.update-product');

        // Sub-Categories Collection API routes
        Route::get('/mcollproducts/vlist', [McategoryController::class,'productsVlist'])->name('mcollproducts.vlist');
        Route::get('/querys/vlist', [McategoryController::class,'querysVlist'])->name('querys.vlist');

        // Browse Banners routes
        Route::get('/browsebanners/list',[BannerController::class,'browseBannerList'])->name('browsebanners.list');
        Route::get('/browsebanners/vlist',[BannerController::class,'browseBannerVlist'])->name('browsebanners.vlist');
        Route::post('/browsebanners/add',[BannerController::class,'addBrowseBanner'])->name('browsebanner.add');
        Route::post('/browsebanners/update',[BannerController::class,'editBrowseBanner'])->name('browsebanner.edit');
        Route::post('/browsebanners/reorder', [BannerController::class, 'reorder']);
        Route::post('/browsebanner-delete', [BannerController::class, 'deleteBrowseBanner']);

        // Main Category api categories->sub-categories->products
        Route::get('/main/categories', [BannerController::class, 'index']);

        // Home Large Banners routes
        // Route::get('/large-banners/list',[HomebannerController::class,'largeBannerList'])->name('largebanners.list');
        // Route::get('/large-banners/vlist',[HomebannerController::class,'largeBannerVlist'])->name('largebanners.vlist');
        // Route::post('/large-banners/add',[HomebannerController::class,'addLargeBanner'])->name('largebanner.add');
        // Route::post('/large-banners/update',[HomebannerController::class,'editLargeBanner'])->name('largebanner.edit');
        // Route::post('/large-banners/reorder', [HomebannerController::class, 'largereorder']);

        // // Home Small Banners routes
        // Route::get('/small-banners/list',[HomebannerController::class,'smallBannerList'])->name('smallbanners.list');
        // Route::get('/small-banners/vlist',[HomebannerController::class,'smallBannerVlist'])->name('smallbanners.vlist');
        // Route::post('/small-banners/add',[HomebannerController::class,'addSmallBanner'])->name('smallbanner.add');
        // Route::post('/small-banners/update',[HomebannerController::class,'editSmallBanner'])->name('smallbanner.edit');
        // Route::post('/small-banners/reorder', [HomebannerController::class, 'smallreorder']);

        // // Home Explore Banners routes
        // Route::get('/explore-deal-banners/list',[HomebannerController::class,'exploreDealBannerList'])->name('exploredealbanners.list');
        // Route::get('/explore-deal-banners/vlist',[HomebannerController::class,'exploreDealBannerVlist'])->name('exploredealbanners.vlist');
        // Route::post('/explore-deal-banners/add',[HomebannerController::class,'addExploreDealBanner'])->name('exploredealbanner.add');
        // Route::post('/explore-deal-banners/update',[HomebannerController::class,'editExploreDealBanner'])->name('exploredealbanner.edit');
        // Route::post('/explore-deal-banners/reorder', [HomebannerController::class, 'exploreDealreorder']);

        // // Home Fruit Banners routes
        // Route::get('/fruit-banners/list',[HomebannerController::class,'fruitBannerList'])->name('fruitbanners.list');
        // Route::get('/fruit-banners/vlist',[HomebannerController::class,'fruitBannerVlist'])->name('fruitbanners.vlist');
        // Route::post('/fruit-banners/add',[HomebannerController::class,'addFruitBanner'])->name('fruitbanner.add');
        // Route::post('/fruit-banners/update',[HomebannerController::class,'editFruitBanner'])->name('fruitbanner.edit');
        // Route::post('/fruit-banners/reorder', [HomebannerController::class, 'fruitreorder']);
    });
});


// Home Routes
Route::get('/home', [HomeController::class, 'index'])->name('home');

// User Routes
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');
