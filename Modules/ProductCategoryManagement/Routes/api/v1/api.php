<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'customer', 'as'=>'customer.', 'namespace' => 'Api\V1\Customer'], function () {
    Route::group(['prefix' => 'productcategory', 'as'=>'productcategory.'], function () {
        Route::get('/', 'CategoryController@index');
        Route::get('childes', 'CategoryController@childes');
        Route::get('childes-products', 'CategoryController@childes_products');
    });
});

