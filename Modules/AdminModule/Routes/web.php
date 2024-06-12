<?php

use Illuminate\Support\Facades\Route;
use Modules\AdminModule\Http\Controllers\Web\Admin\Report\BookingReportController;
use Modules\AdminModule\Http\Controllers\Web\Admin\Report\Business\EarningReportController;
use Modules\AdminModule\Http\Controllers\Web\Admin\Report\Business\ExpenseReportController;
use Modules\AdminModule\Http\Controllers\Web\Admin\Report\Business\OverviewReportController;
use Modules\AdminModule\Http\Controllers\Web\Admin\Report\ProviderReportController;
use Modules\AdminModule\Http\Controllers\Web\Admin\Report\TransactionReportController;
use Modules\AdminModule\Http\Controllers\Web\Admin\Report\OrderReportController;

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

//employee management
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Web\Admin', 'middleware' => ['admin','mpc:employee_management']], function () {
    Route::get('dashboard', 'AdminController@dashboard')->name('dashboard')->withoutMiddleware(['mpc:employee_management']);
    Route::get('update-dashboard-earning-graph', 'AdminController@update_dashboard_earning_graph')->name('update-dashboard-earning-graph');

    //profile
    Route::get('profile-update', 'AdminController@profile_info')->name('profile_update');
    Route::post('profile-update', 'AdminController@update_profile');
    Route::get('get-updated-data', 'AdminController@get_updated_data')->name('get_updated_data');

    Route::get('cache-clear', 'AdminController@clearCache')->name('cache.clear');

    Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
        Route::any('create', 'RoleController@create')->name('create');
        Route::post('store', 'RoleController@store')->name('store');
        Route::get('edit/{id}', 'RoleController@edit')->name('edit');
        Route::put('update/{id}', 'RoleController@update')->name('update');
        Route::any('status-update/{id}', 'RoleController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'RoleController@destroy')->name('delete');
    });

    Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
        Route::any('list', 'EmployeeController@index')->name('index');
        Route::any('create', 'EmployeeController@create')->name('create');
        Route::post('store', 'EmployeeController@store')->name('store');
        Route::get('edit/{id}', 'EmployeeController@edit')->name('edit');
        Route::put('update/{id}', 'EmployeeController@update')->name('update');
        Route::any('status-update/{id}', 'EmployeeController@status_update')->name('status-update');
        Route::delete('delete/{id}', 'EmployeeController@destroy')->name('delete');
        Route::any('download', 'EmployeeController@download')->name('download');
    });
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Web\Admin'], function () {
    Route::group(['prefix' => 'report', 'as' => 'report.', 'namespace' => 'Report'], function () {
        //Transaction Report
        Route::any('transaction', [TransactionReportController::class, 'get_transaction_report'])->name('transaction');
        Route::any('transaction/download', [TransactionReportController::class, 'download_transaction_report'])->name('transaction.download');

        //Booking Report
        Route::any('booking', [BookingReportController::class, 'get_booking_report'])->name('booking');
        Route::any('booking/download', [BookingReportController::class, 'get_booking_report_download'])->name('booking.download');

        //Provider Report
        Route::any('provider', [ProviderReportController::class, 'get_provider_report'])->name('provider');
        Route::any('provider/download', [ProviderReportController::class, 'get_provider_report_download'])->name('provider.download');

        //Order Report
        Route::any('order', [OrderReportController::class, 'get_order_report'])->name('order');
        Route::any('order/download', [OrderReportController::class, 'get_order_report_download'])->name('order.download');

        //Business Report
        Route::group(['prefix' => 'business', 'as' => 'business.'], function () {
            Route::any('overview', [OverviewReportController::class, 'get_business_overview_report'])->name('overview');
            Route::any('overview/download', [OverviewReportController::class, 'get_business_overview_report_download'])->name('overview.download');
            Route::any('earning', [EarningReportController::class, 'get_business_earning_report'])->name('earning');
            Route::any('earning/download', [EarningReportController::class, 'get_business_earning_report_download'])->name('earning.download');
            Route::any('expense', [ExpenseReportController::class, 'get_business_expense_report'])->name('expense');
            Route::any('expense/download', [ExpenseReportController::class, 'get_business_expense_report_download'])->name('expense.download');
        });
    });
});
