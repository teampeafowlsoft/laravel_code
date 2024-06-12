<?php

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
use Illuminate\Support\Facades\Route;

Route::prefix('productmanagement')->group(function() {
    Route::get('/', 'ProductManagementController@index');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Web\Admin', 'middleware' => ['admin','mpc:product_management_module']], function () {
    Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
        Route::any('list', 'ProductManagementController@index')->name('index');
        Route::any('create', 'ProductManagementController@create')->name('create');
        Route::post('store', 'ProductManagementController@store')->name('store');
//        Route::any('detail/{id}', 'ProductManagementController@show')->name('detail');
        Route::get('edit/{id}/{group_id}', 'ProductManagementController@edit')->name('edit');
        Route::put('update/{id}', 'ProductManagementController@update')->name('update');
        Route::any('status-update/{id}', 'ProductManagementController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'ProductManagementController@destroy')->name('delete');
        Route::put('review-update/{id}', 'ProductManagementController@review_update')->name('review-update');
        Route::any('download', 'ProductManagementController@download')->name('download');

        //Category ajax routes
        Route::any('ajax-add-category', 'ProductManagementController@ajax_add_category')->name('ajax-add-category')->withoutMiddleware('csrf');

        Route::get('access-method', 'ProductManagementController@access_method')->name('access-method');
        Route::get('get-sku', 'ProductManagementController@getSKU')->name('get-sku');
        Route::get('get-sku-arabic', 'ProductManagementController@getSKUarabic')->name('get-sku-arabic');

        //ajax routes
        Route::any('ajax-add-variant', 'ProductManagementController@ajax_add_variant')->name('ajax-add-variant')->withoutMiddleware('csrf');
        Route::any('ajax-remove-variant/{variant_key}', 'ProductManagementController@ajax_remove_variant')->name('ajax-remove-variant')->withoutMiddleware('csrf');

        Route::get('ajax-switch-attribute', 'ProductManagementController@ajax_switch_attribute')->name('ajax-switch-attribute');

        Route::get('arabic-ajax-switch-attribute', 'ProductManagementController@arabic_ajax_switch_attribute')->name('arabic-ajax-switch-attribute');

        Route::any('pendinglist', 'ProductManagementController@pendinglist')->name('pendinglist');
        Route::get('view/{id}/{group_id}', 'ProductManagementController@view')->name('view');

        Route::any('bulk_upload', 'ProductManagementController@bulk_upload')->name('bulk_upload');
        Route::post('import', 'ProductManagementController@import')->name('import');

    });

    Route::group(['prefix' => 'productcampaign', 'as' => 'productcampaign.'], function () {
        Route::any('create', 'ProductcampaignController@create')->name('create');
        Route::any('list', 'ProductcampaignController@index')->name('list');
        Route::post('store', 'ProductcampaignController@store')->name('store');
        Route::get('edit/{id}', 'ProductcampaignController@edit')->name('edit');
        Route::put('update/{id}', 'ProductcampaignController@update')->name('update');
        Route::any('status-update/{id}', 'ProductcampaignController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'ProductcampaignController@destroy')->name('delete');
        Route::any('download', 'ProductcampaignController@download')->name('download');
    });

    Route::group(['prefix' => 'productcoupon', 'as' => 'productcoupon.'], function () {
        Route::any('create', 'ProductcouponController@create')->name('create');
        Route::any('list', 'ProductcouponController@index')->name('list');
        Route::post('store', 'ProductcouponController@store')->name('store');
        Route::get('edit/{id}', 'ProductcouponController@edit')->name('edit');
        Route::put('update/{id}', 'ProductcouponController@update')->name('update');
        Route::any('status-update/{id}', 'ProductcouponController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'ProductcouponController@destroy')->name('delete');
        Route::any('download', 'ProductcouponController@download')->name('download');
    });
});

Route::group(['prefix' => 'provider', 'as' => 'provider.', 'namespace' => 'Web\Provider', 'middleware' => ['provider']], function () {

    Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
        Route::any('list', 'ProductManagementController@index')->name('index');
        Route::any('create', 'ProductManagementController@create')->name('create');
        Route::post('store', 'ProductManagementController@store')->name('store');
        Route::get('edit/{id}/{group_id}', 'ProductManagementController@edit')->name('edit');
        Route::put('update/{id}', 'ProductManagementController@update')->name('update');
        Route::any('status-update/{id}', 'ProductManagementController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'ProductManagementController@destroy')->name('delete');
        Route::any('pendinglist', 'ProductManagementController@pendinglist')->name('pendinglist');
        Route::get('view/{id}/{group_id}', 'ProductManagementController@view')->name('view');
        Route::get('ajax-childes-multiple/{id}', 'ProductManagementController@ajax_childes_multiple')->name('ajax-childes-multiple');

        Route::get('ajax-childes-arabic-multiple/{id}', 'ProductManagementController@ajax_childes_arabic_multiple')->name('ajax-childes-arabic-multiple');

        Route::get('provider-ajax-switch-attribute', 'ProductManagementController@provider_ajax_switch_attribute')->name('provider-ajax-switch-attribute');

        Route::get('provider-arabic-ajax-switch-attribute', 'ProductManagementController@provider_arabic_ajax_switch_attribute')->name('provider-arabic-ajax-switch-attribute');

        Route::get('provider-access-method', 'ProductManagementController@provider_access_method')->name('provider-access-method');

        Route::get('provider-get-sku', 'ProductManagementController@providergetSKU')->name('provider-get-sku');
        Route::get('provider-get-sku-arabic', 'ProductManagementController@providergetSKUarabic')->name('provider-get-sku-arabic');

    });
});
