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

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Web\Admin', 'middleware' => ['admin','mpc:service_management']], function () {

    Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
        Route::any('create', 'CategoryController@create')->name('create');
        Route::post('store', 'CategoryController@store')->name('store');
        Route::get('edit/{id}/{group_id}', 'CategoryController@edit')->name('edit');
        Route::put('update/{id}', 'CategoryController@update')->name('update');
        Route::any('status-update/{id}', 'CategoryController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'CategoryController@destroy')->name('delete');
        Route::get('childes', 'CategoryController@childes');
        Route::get('ajax-childes/{id}', 'CategoryController@ajax_childes')->name('ajax-childes');
        Route::get('ajax-childes-arabic/{id}', 'CategoryController@ajax_childes_arabic')->name('ajax-childes-arabic');
        Route::get('ajax-childes-multiple/{id}', 'CategoryController@ajax_childes_multiple')->name('ajax-childes-multiple');
        Route::get('ajax-childes-arabic-multiple/{id}', 'CategoryController@ajax_childes_arabic_multiple')->name('ajax-childes-arabic-multiple');
        Route::get('ajax-childes-only/{id}', 'CategoryController@ajax_childes_only')->name('ajax-childes-only');
        Route::get('download', 'CategoryController@download')->name('download');
        Route::get('lang-translate', 'CategoryController@lang_translate')->name('lang-translate');
    });

    Route::group(['prefix' => 'main-category', 'as' => 'main-category.'], function () {
        Route::any('create', 'MainCategoryController@create')->name('create');
        Route::post('store', 'MainCategoryController@store')->name('store');
        Route::any('status-update/{id}', 'MainCategoryController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'MainCategoryController@destroy')->name('delete');
        Route::get('edit/{id}/{group_id}', 'MainCategoryController@edit')->name('edit');
        Route::put('update/{id}', 'MainCategoryController@update')->name('update');
    });

    Route::group(['prefix' => 'sub-category', 'as' => 'sub-category.'], function () {
        Route::any('create', 'SubCategoryController@create')->name('create');
        Route::post('store', 'SubCategoryController@store')->name('store');
        Route::get('edit/{id}/{group_id}', 'SubCategoryController@edit')->name('edit');
        Route::put('update/{id}', 'SubCategoryController@update')->name('update');
        Route::any('status-update/{id}', 'SubCategoryController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'SubCategoryController@destroy')->name('delete');
        Route::get('download', 'SubCategoryController@download')->name('download');
        Route::get('lang-translate', 'SubCategoryController@lang_translate')->name('lang-translate');
    });
});
