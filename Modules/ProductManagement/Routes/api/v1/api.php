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
//Route::group(['prefix' => 'customer', 'as' => 'customer.', 'namespace' => 'Api\V1\Customer', 'middleware' => ['auth:api']], function () {

    Route::group(['prefix' => 'customer', 'as' => 'customer.', 'namespace' => 'Api\V1\Customer'], function () {
//Route::group(['prefix' => 'customer', 'as' => 'customer.', 'namespace' => 'Api\V1\Customer'], function () {
        Route::group(['prefix' => 'product'], function () {

            Route::get('/', 'ProductController@index');
            Route::get('search', 'ProductController@search');
            Route::get('popular', 'ProductController@popular');
            Route::get('recommended', 'ProductController@recommended');
            Route::get('search_recommended', 'ProductController@search_recommended');
            Route::get('detail', 'ProductController@product_details');
            Route::any('product-list/{main_category_id}', 'ProductController@product_by_maincategory');
            
            Route::group(['middleware'=>'auth:api'],function (){
//                Route::get('/', 'ProductController@index');
//        Route::get('search', 'ProductController@search');
//        Route::get('popular', 'ProductController@popular');
//        Route::get('recommended', 'ProductController@recommended');
//        Route::get('search_recommended', 'ProductController@search_recommended');
                Route::get('trending', 'ProductController@trending');
//        Route::get('recently_viewed', 'ProductController@recently_viewed');
                Route::get('company_wise', 'ProductController@company_wise');
//        Route::get('detail', 'ProductController@product_details');
                Route::post('add_to_cart', 'ProductController@add_to_cart_product');
                Route::get('list', 'ProductController@cart_product_list');
//        Route::any('product-list/{main_category_id}', 'ProductController@product_by_maincategory');
                Route::post('product-cart-update', 'ProductController@cart_update');
                Route::delete('remove/{id}', 'ProductController@remove');
                Route::post('product-cart-remove', 'ProductController@remove_cart');
                Route::delete('data/empty', 'ProductController@empty_cart');
                Route::post('request/send', 'ProductController@place_request')->middleware('hitLimiter');
                Route::get('orderlist', 'ProductController@order_item_list');
                Route::get('product_booking_list', 'ProductController@product_booking_list');
                Route::get('/{booking_id}', 'ProductController@show');
                Route::put('status-update/{order_booking_id}', 'ProductController@status_update');
            });

        });

        Route::resource('productcampaign', 'ProductCampaignController', ['only' => ['index']]);
        Route::group(['prefix' => 'productcampaign', 'as' => 'productcampaign.', 'middleware' => ['auth:api']], function () {
            Route::get('data/items', 'ProductCampaignController@campaign_items')->withoutMiddleware('auth:api');
        });

    });


//Route::group(['prefix' => 'customer', 'as' => 'customer.', 'namespace' => 'Api\V1\Customer'], function () {
//    Route::group(['prefix' => 'product'], function () {
//
//    });
//});
//
//Route::group(['prefix' => 'customer', 'as' => 'customer.', 'namespace' => 'Api\V1\Customer'], function () {
//    Route::group(['prefix' => 'product'], function () {
//        Route::get('popular', 'ProductController@popular');
//    });
//});
