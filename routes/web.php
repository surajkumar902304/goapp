<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\McategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TagController;
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
        // Products routes
        Route::get('/products/list',[AdminController::class,'productsList'])->name('products.list');
        Route::get('/products/vlist',[AdminController::class,'adminProductlist'])->name('products.vlist');
        Route::get('/product/addview',[AdminController::class,'productAddView'])->name('adminproduct.addview');
        Route::get('/product/pdata',[AdminController::class,'productAddData'])->name('adminproduct.pdata');
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
        // Shops routes
        Route::get('/shops/vlist',[AdminController::class,'adminShoplist'])->name('shops.vlist');
        Route::post('/shop/add',[AdminController::class,'adminAddShop'])->name('shop.add');
        Route::post('/shop/add/nuser',[AdminController::class,'addUserToShop'])->name('shop.add.nuser');
        Route::post('/shop/add/owner',[AdminController::class,'addOwnertoShop'])->name('shop.add.owner');
        Route::post('/shop-toggle-status', [AdminController::class, 'toggleStatus']);
        // Product Variations routes
        Route::post('/save-product', [AdminController::class, 'productStoreData'])->name('adminproduct.storedata');
        Route::post('/mproduct-types', [AdminController::class, 'storeProductType']);
        Route::post('/mbrands', [AdminController::class, 'storeBrand']);
        Route::post('/mtags', [AdminController::class, 'storeTag']);
        Route::get('/product/{mproduct_id}',[AdminController::class,'adminProductEdit'])->name('adminproduct.edit');
        Route::get('/vproduct/editdata',[AdminController::class,'productEditData'])->name('adminproduct.editdata');
        Route::post('/update-product', [AdminController::class, 'updateProductData'])->name('adminproduct.update-product');
        // Categories routes
        Route::get('/mcategories/list',[McategoryController::class,'index'])->name('mcats.list');
        Route::get('/mcategories/vlist',[McategoryController::class,'mcatVlist'])->name('mcats.vlist');
        Route::post('/mcategory/add',[McategoryController::class,'addMcat'])->name('mcat.add');
        Route::post('/mcategory/update',[McategoryController::class,'editMcat'])->name('mcat.edit');
        // Sub-Categories routes
        Route::get('/msub-categories/list',[McategoryController::class,'mcatsubList'])->name('msubcats.list');
        Route::get('/msub-categories/vlist',[McategoryController::class,'mcatsubVlist'])->name('msubcats.vlist');
        Route::post('/msub-category/add',[McategoryController::class,'addMsubcat'])->name('msubcat.add');
        Route::post('/msub-category/update',[McategoryController::class,'editMsubcat'])->name('msubcat.edit');
        Route::get('/msub-category/add',[McategoryController::class,'addViewMsubcat'])->name('mcoll.add');

        // Sub-Categories Collection API routes
        Route::get('/mcollproducts/vlist', [McategoryController::class,'productsVlist'])->name('mcollproducts.vlist');
        Route::get('/querys/vlist', [McategoryController::class,'querysVlist'])->name('querys.vlist');
    });
});


// Home Routes
Route::get('/home', [HomeController::class, 'index'])->name('home');

// User Routes
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/approval', [HomeController::class, 'approval'])->name('approval.page');
Route::post('/select-shop', [HomeController::class, 'selectShop'])->name('select.shop');
Route::get('/mangeuser', [HomeController::class, 'mangeuser'])->name('mange.user');


// Product type Routes
Route::get('/product-types', [ProductTypeController::class, 'index'])->name('product_types.index');

// Brand Routes
Route::get('/brands',[BrandController::class,'index'])->name('brands.index');

// Tag Routes
Route::get('/tags',[TagController::class,'index'])->name('tags.index');

// Shop Routes
Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
Route::get('/optioname', [ShopController::class, 'optioname'])->name('optioname');


// Product Routes
Route::middleware(['auth'])->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('product.index');
    Route::get('/add', [ProductController::class, 'add'])->name('product.add');
    Route::post('/', [ProductController::class, 'store'])->name('product.store');
    Route::get('/{product_id}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/{product_id}', [ProductController::class, 'update'])->name('product.update');
    Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('product.duplicate');

});

// Category Routes
Route::middleware(['auth'])->prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/add', [CategoryController::class, 'add'])->name('category.add');
    Route::post('/', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/{cat_id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/{cat_id}', [CategoryController::class, 'update'])->name('category.update');
    Route::get('/{cat_id}/view', [CategoryController::class, 'view'])
    ->where('cat_id', '[0-9]+')
    ->name('category.view');
});
