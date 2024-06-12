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

Route::prefix('attributemanagement')->group(function() {
    Route::get('/', 'AttributeManagementController@index');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Web\Admin', 'middleware' => ['admin','mpc:attribute_management_module']], function () {
    Route::group(['prefix' => 'attribute', 'as' => 'attribute.'], function () {
        Route::any('list', 'AttributeManagementController@index')->name('index');
        Route::any('create', 'AttributeManagementController@create')->name('create');
        Route::post('store', 'AttributeManagementController@store')->name('store');
        Route::any('status-update/{id}', 'AttributeManagementController@status_update')->name('status-update');
        Route::get('edit/{id}/{group_id}', 'AttributeManagementController@edit')->name('edit');
        Route::put('update/{id}', 'AttributeManagementController@update')->name('update');
        Route::delete('delete/{id}', 'AttributeManagementController@destroy')->name('delete');

    });

//    Attribute Value Routes
    Route::group(['prefix' => 'attributeval', 'as' => 'attributeval.'], function () {
        Route::any('list/{group_id}', 'AttributeValueController@index')->name('index');
        Route::any('create/{group_id}', 'AttributeValueController@create')->name('create');
        Route::post('store', 'AttributeValueController@store')->name('store');
        Route::get('edit/{id}/{group_id}', 'AttributeValueController@edit')->name('edit');
        Route::any('status-update/{id}', 'AttributeValueController@status_update')->name('status-update');
        Route::put('update/{id}', 'AttributeValueController@update')->name('update');
        Route::delete('delete/{id}', 'AttributeValueController@destroy')->name('delete');
    });
});
