<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    //Domain Verification
    Route::domain(env('USER_DOMAIN'))->group(function () {
        //Api Key Protection
        Route::middleware('auth.apikey')->group(function () {
            //Giving v1 prefix
            Route::name('v1.')->prefix('v1')->group(function () {
                //Giving Load Prefix
                Route::name('load.')->prefix('load')->group(function () {
                    //Display Wallet Balance
                    Route::post('wallet-balance', 'LoadController@wallet')->name('wallet-balance');
                });
            });
        });
    });
});
