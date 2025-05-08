<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BannerController;
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
        // Brands routes
        Route::get('/mbrands/list',[AdminController::class,'mbrandList'])->name('mbrands.list');
        Route::get('/mbrands/vlist',[AdminController::class,'mbrandVlist'])->name('mbrands.vlist');
        Route::post('/mbrands/add',[AdminController::class,'addBrand'])->name('mbrand.add');
        Route::post('/mbrands/update',[AdminController::class,'editBrand'])->name('mbrand.edit');

        // Products routes
        Route::get('/products/list',[AdminController::class,'productsList'])->name('products.list');
        Route::get('/products/vlist',[AdminController::class,'adminProductlist'])->name('products.vlist');
        Route::get('/product/addview',[AdminController::class,'productAddView'])->name('adminproduct.addview');
        Route::get('/product/pdata',[AdminController::class,'productAddData'])->name('adminproduct.pdata');
        
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

        // Categories routes
        Route::get('/mcategories/list',[McategoryController::class,'index'])->name('mcats.list');
        Route::get('/mcategories/vlist',[McategoryController::class,'mcatVlist'])->name('mcats.vlist');
        Route::post('/mcategory/add',[McategoryController::class,'addMcat'])->name('mcat.add');
        Route::post('/mcategory/update',[McategoryController::class,'editMcat'])->name('mcat.edit');

        // Sub-Categories routes
        Route::get('/msub-categories/list',[McategoryController::class,'mcatsubList'])->name('msubcats.list');
        Route::get('/msub-categories/vlist',[McategoryController::class,'mcatsubVlist'])->name('msubcats.vlist');
        Route::post('/msub-category/add',[McategoryController::class,'addMsubcat'])->name('msubcat.add');
        Route::get('/msub-category/add',[McategoryController::class,'addViewMsubcat'])->name('mcoll.add');
        
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
        Route::get('/main/categories', [BannerController::class, 'index']);
    });
});


// Home Routes
Route::get('/home', [HomeController::class, 'index'])->name('home');

// User Routes
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');
