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
Route::get('generate-password', function () {
    return Hash::make('password');
});

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Route::name('admin.')->group(function () {
//     Route::get('users', 'Admin\UserController@index')->name('users');

// });

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function(){
    Route::name('pengguna.')->group(function () {
        Route::get('pengguna/index', 'Admin\Pengguna@index')->name('index');
        Route::get('pengguna/get/{id}', 'Admin\Pengguna@edit')->name('get');
        Route::post('pengguna/update', 'Admin\Pengguna@update')->name('update');
        Route::post('pengguna/store', 'Admin\Pengguna@store')->name('store');
        Route::delete('pengguna/delete/{id}', 'Admin\Pengguna@destroy')->name('delete');
        Route::get('pengguna/get-datatables-all-data', 'Admin\Pengguna@dataTablesGetAllData')->name('datatablesGetalldata');
    
    });

    Route::name('roles.')->group(function() {
        Route::get('roles/index', 'Admin\Roles@index')->name('index');
    });

    Route::name('siswa.')->group(function() {
        Route::get('siswa/index', 'Admin\siswas@index')->name('index');
        Route::get('siswa/get/{id}', 'Admin\siswas@edit')->name('get');
        Route::post('siswa/update', 'Admin\siswas@update')->name('update');
        Route::post('siswa/store', 'Admin\siswas@store')->name('store');
        Route::post('siswa/store-excel-data', 'Admin\siswas@storeExcelData')->name('storeExcel');
        Route::delete('siswa/delete/{id}', 'Admin\siswas@destroy')->name('delete');
        Route::get('siswa/get-datatables-all-data', 'Admin\siswas@dataTablesGetAllData')->name('datatablesGetalldata');
    });

    Route::name('pelajaran.')->group(function() {
        Route::get('pelajaran/index', 'Admin\MapelLintasMinat@index')->name('index');
        Route::get('pelajaran/get/{id}', 'Admin\MapelLintasMinat@edit')->name('get');
        Route::post('pelajaran/update', 'Admin\MapelLintasMinat@update')->name('update');
        Route::post('pelajaran/store', 'Admin\MapelLintasMinat@store')->name('store');
        Route::delete('pelajaran/delete/{id}', 'Admin\MapelLintasMinat@destroy')->name('delete');
        Route::get('pelajaran/get-datatables-all-data', 'Admin\MapelLintasMinat@dataTablesGetAllData')->name('datatablesGetalldata');
    });

    Route::name('lintas-minat.')->group(function() {
        Route::get('lintas-minat/index', 'Admin\LintasMinat@index')->name('index');
        Route::get('lintas-minat/detail/{id}', 'Admin\LintasMinat@show')->name('detail');
        Route::get('lintas-minat/get/{id}', 'Admin\LintasMinat@edit')->name('get');
        Route::post('lintas-minat/update', 'Admin\LintasMinat@update')->name('update');
        Route::post('lintas-minat/store', 'Admin\LintasMinat@store')->name('store');
        Route::delete('lintas-minat/delete/{id}', 'Admin\LintasMinat@destroy')->name('delete');
        Route::delete('lintas-minat/delete-all', 'Admin\LintasMinat@deleteAll')->name('deleteAll');
        Route::get('lintas-minat/get-datatables-all-data', 'Admin\LintasMinat@dataTablesGetAllData')->name('datatablesGetalldata');
        Route::get('lintas-minat/get-datatables-detail-data/{name}', 'Admin\LintasMinat@dataTablesGetDetailData')->name('datatablesGetdetaildata');
        Route::get('lintas-minat/download-data/{name}', 'Admin\LintasMinat@detailClassToExcel')->name('downloadExcelData');
    });

    Route::name('kalkulasi.')->group(function() {
        Route::get('kalkulasi/do-calculation', 'Admin\Kalkulasi@calculation')->name('calculation');
        Route::get('kalkulasi/do-class-division', 'Admin\Kalkulasi@classCalculation')->name('classCalculation');
        Route::get('kalkulasi/index', 'Admin\Kalkulasi@index')->name('index');
        Route::get('kalkulasi/get/{id}', 'Admin\Kalkulasi@edit')->name('get');
        Route::post('kalkulasi/update', 'Admin\Kalkulasi@update')->name('update');
        Route::post('kalkulasi/store', 'Admin\Kalkulasi@store')->name('store');
        Route::delete('kalkulasi/delete/{id}', 'Admin\Kalkulasi@destroy')->name('delete');
        Route::get('kalkulasi/get-datatables-all-data', 'Admin\Kalkulasi@dataTablesGetAllData')->name('datatablesGetalldata');
    });
    // Route::get('users', 'Admin\UserController@index')->name('users');
});