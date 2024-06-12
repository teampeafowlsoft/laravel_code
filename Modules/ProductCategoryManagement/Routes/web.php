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

//Route::prefix('productcategorymanagement')->group(function() {
//    Route::get('/', 'ProductCategoryManagementController@index');
//});


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

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Web\Admin', 'middleware' => ['admin', 'mpc:service_management']], function () {

    Route::group(['prefix' => 'productcategory', 'as' => 'productcategory.'], function () {
        Route::any('create', 'ProductCategoryManagementController@create')->name('create');
        Route::post('store', 'ProductCategoryManagementController@store')->name('store');
        Route::get('edit/{id}/{group_id}', 'ProductCategoryManagementController@edit')->name('edit');
        Route::put('update/{id}', 'ProductCategoryManagementController@update')->name('update');
        Route::any('status-update/{id}', 'ProductCategoryManagementController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'ProductCategoryManagementController@destroy')->name('delete');
        Route::get('childes', 'ProductCategoryManagementController@childes');
        Route::get('ajax-childes/{id}', 'ProductCategoryManagementController@ajax_childes')->name('ajax-childes');
        Route::get('ajax-childes-arabic/{id}', 'ProductCategoryManagementController@ajax_childes_arabic')->name('ajax-childes-arabic');
        Route::get('ajax-childes-multiple/{id}', 'ProductCategoryManagementController@ajax_childes_multiple')->name('ajax-childes-multiple');
        Route::get('ajax-childes-arabic-multiple/{id}', 'ProductCategoryManagementController@ajax_childes_arabic_multiple')->name('ajax-childes-arabic-multiple');
        Route::get('ajax-childes-only/{id}', 'ProductCategoryManagementController@ajax_childes_only')->name('ajax-childes-only');
        Route::get('download', 'ProductCategoryManagementController@download')->name('download');
        Route::get('lang-translate', 'ProductCategoryManagementController@lang_translate')->name('lang-translate');
        Route::get('zone-select', 'ProductCategoryManagementController@zone_select')->name('zone-select');
    });

    Route::group(['prefix' => 'productsub-category', 'as' => 'productsub-category.'], function () {
        Route::any('create', 'ProductSubcategoryController@create')->name('create');
        Route::post('store', 'ProductSubcategoryController@store')->name('store');
        Route::get('edit/{id}/{group_id}', 'ProductSubcategoryController@edit')->name('edit');
        Route::put('update/{id}', 'ProductSubcategoryController@update')->name('update');
        Route::any('status-update/{id}', 'ProductSubcategoryController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'ProductSubcategoryController@destroy')->name('delete');
        Route::get('download', 'ProductSubcategoryController@download')->name('download');
        Route::get('lang-translate', 'ProductSubcategoryController@lang_translate')->name('lang-translate');
    });
});

