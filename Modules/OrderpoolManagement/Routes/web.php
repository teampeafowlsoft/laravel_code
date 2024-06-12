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

Route::prefix('orderpoolmanagement')->group(function() {
    Route::get('/', 'OrderpoolManagementController@index');
});


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Web\Admin', 'middleware' => ['admin','mpc:orderpool_management']], function () {

    Route::group(['prefix' => 'orderpool', 'as' => 'orderpool.'], function () {
        Route::any('list', 'OrderpoolManagementController@index')->name('list');
//        Route::get('check', 'BookingController@check_booking')->name('check');
        Route::get('details/{id}', 'OrderpoolManagementController@details')->name('details');
        Route::get('status-update/{id}', 'OrderpoolManagementController@status_update')->name('status_update');
        Route::get('payment-update/{id}', 'OrderpoolManagementController@payment_update')->name('payment_update');
//        Route::any('schedule-update/{id}', 'BookingController@schedule_upadte')->name('schedule_update');
//        Route::get('serviceman-update/{id}', 'BookingController@serviceman_update')->name('serviceman_update');
        Route::any('download', 'OrderpoolManagementController@download')->name('download');
        Route::any('invoice/{id}', 'OrderpoolManagementController@invoice')->name('invoice');    });
});

Route::group(['prefix' => 'provider', 'as' => 'provider.', 'namespace' => 'Web\Provider', 'middleware' => ['provider']], function () {

    Route::group(['prefix' => 'orderpool', 'as' => 'orderpool.'], function () {
        Route::any('list', 'OrderpoolManagementController@index')->name('list');
//        Route::get('check', 'BookingController@check_booking')->name('check');
        Route::get('details/{id}', 'OrderpoolManagementController@details')->name('details');
        Route::get('status-update/{id}', 'OrderpoolManagementController@status_update')->name('status_update');
        Route::get('payment-update/{id}', 'OrderpoolManagementController@payment_update')->name('payment_update');
        Route::any('download', 'OrderpoolManagementController@download')->name('download');
        Route::any('invoice/{id}', 'OrderpoolManagementController@invoice')->name('invoice');
    });
});
