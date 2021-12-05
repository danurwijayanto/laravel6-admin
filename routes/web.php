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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Route::name('admin.')->group(function () {
//     Route::get('users', 'Admin\UserController@index')->name('users');

// });

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function(){
    Route::name('dashboard.')->group(function () {
        Route::get('chart-data', 'HomeController@getChartData')->name('getChartData');

    });

    Route::name('user.')->group(function () {
        Route::get('user/index', 'Admin\UserController@index')->name('index');
        Route::get('user/get/{id}', 'Admin\UserController@edit')->name('get');
        Route::post('user/update', 'Admin\UserController@update')->name('update');
        Route::post('user/store', 'Admin\UserController@store')->name('store');
        Route::delete('user/delete/{id}', 'Admin\UserController@destroy')->name('delete');
        Route::get('user/get-datatables-all-data', 'Admin\UserController@dataTablesGetAllData')->name('datatablesGetalldata');

    });

    Route::name('roles.')->group(function() {
        Route::get('roles/index', 'Admin\RoleController@index')->name('index');
    });

    Route::name('student.')->group(function() {
        Route::get('student/index', 'Admin\SiswaController@index')->name('index');
        Route::get('student/get/{id}', 'Admin\SiswaController@edit')->name('get');
        Route::post('student/update', 'Admin\SiswaController@update')->name('update');
        Route::post('student/store', 'Admin\SiswaController@store')->name('store');
        Route::post('student/store-excel-data', 'Admin\SiswaController@storeExcelData')->name('storeExcel');
        Route::delete('student/delete/{id}', 'Admin\SiswaController@destroy')->name('delete');
        Route::get('student/get-datatables-all-data', 'Admin\SiswaController@dataTablesGetAllData')->name('datatablesGetalldata');
    });

    Route::name('course.')->group(function() {
        Route::get('course/index', 'Admin\MapelController@index')->name('index');
        Route::get('course/get/{id}', 'Admin\MapelController@edit')->name('get');
        Route::post('course/update', 'Admin\MapelController@update')->name('update');
        Route::post('course/store', 'Admin\MapelController@store')->name('store');
        Route::delete('course/delete/{id}', 'Admin\MapelController@destroy')->name('delete');
        Route::get('course/get-datatables-all-data', 'Admin\MapelController@dataTablesGetAllData')->name('datatablesGetalldata');
    });

    Route::name('crossInterestClass.')->group(function() {
        Route::get('cross-interest/index', 'Admin\LintasMinatController@index')->name('index');
        Route::get('cross-interest/detail/{id}', 'Admin\LintasMinatController@show')->name('detail');
        Route::get('cross-interest/get/{id}', 'Admin\LintasMinatController@edit')->name('get');
        Route::post('cross-interest/update', 'Admin\LintasMinatController@update')->name('update');
        Route::post('cross-interest/store', 'Admin\LintasMinatController@store')->name('store');
        Route::delete('cross-interest/delete/{id}', 'Admin\LintasMinatController@destroy')->name('delete');
        Route::delete('cross-interest/delete-all', 'Admin\LintasMinatController@deleteAll')->name('deleteAll');
        Route::get('cross-interest/get-datatables-all-data', 'Admin\LintasMinatController@dataTablesGetAllData')->name('datatablesGetalldata');
        Route::get('cross-interest/get-datatables-detail-data/{name}', 'Admin\LintasMinatController@dataTablesGetDetailData')->name('datatablesGetdetaildata');
        Route::get('cross-interest/download-data/{name}', 'Admin\LintasMinatController@detailClassToExcel')->name('downloadExcelData');
    });

    Route::name('calresult.')->group(function() {
        Route::get('calresult/do-calculation', 'Admin\KalkulasiController@calculation')->name('calculation');
        Route::get('calresult/do-class-division', 'Admin\KalkulasiController@classCalculation')->name('classCalculation');
        Route::get('calresult/index', 'Admin\KalkulasiController@index')->name('index');
        Route::get('calresult/get/{id}', 'Admin\KalkulasiController@edit')->name('get');
        Route::post('calresult/update', 'Admin\KalkulasiController@update')->name('update');
        Route::post('calresult/store', 'Admin\KalkulasiController@store')->name('store');
        Route::delete('calresult/delete/{id}', 'Admin\KalkulasiController@destroy')->name('delete');
        Route::get('calresult/get-datatables-all-data', 'Admin\KalkulasiController@dataTablesGetAllData')->name('datatablesGetalldata');
    });
    // Route::get('users', 'Admin\UserController@index')->name('users');
});
