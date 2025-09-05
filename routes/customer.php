<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\Auth\LoginController;
use App\Http\Controllers\Customer\RepCustomerController;


Route::prefix('rep')->name('customer.')->group(function () {

    Route::middleware('customer.guest')->group(function () {
        Route::get ('/login',  [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login',  [LoginController::class, 'customerLogin'])->name('login.attempt');
    });


    Route::middleware('customer.auth')->group(function () {

        Route::get('/logout', [LoginController::class, 'customerLogout'])->name('logout');

        Route::get ('/dashboard/vlist',  [RepCustomerController::class, 'index']);
        Route::get ('/vlist',  [RepCustomerController::class, 'customerRepVlist']);
        Route::get ('/order-Commission/vlist',  [RepCustomerController::class, 'customerRepCommission']);

        Route::get('{any}', fn () => view('customer.app'))
            ->where('any', '.*')->name('spa');
    });

});

