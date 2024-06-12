<?php
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

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Web\Admin', 'middleware' => ['admin','mpc:country_management_module']], function () {
    Route::group(['prefix' => 'country', 'as' => 'country.'], function () {
        Route::any('create', 'CountryManagementController@create')->name('create');

        Route::post('store', 'CountryManagementController@store')->name('store');
        Route::get('edit/{id}/{group_id}', 'CountryManagementController@edit')->name('edit');
        Route::put('update/{id}', 'CountryManagementController@update')->name('update');
//        Route::put('get-active-zones/{id}', 'ZoneController@get_active_zones')->name('get-active-zones');
        Route::any('status-update/{id}', 'CountryManagementController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'CountryManagementController@destroy')->name('delete');
        Route::get('download', 'CountryManagementController@download')->name('download');
    });
});
