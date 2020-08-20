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
        Route::get('student/index', 'Admin\StudentController@index')->name('index');
        Route::get('student/get/{id}', 'Admin\StudentController@edit')->name('get');
        Route::post('student/update', 'Admin\StudentController@update')->name('update');
        Route::post('student/store', 'Admin\StudentController@store')->name('store');
        Route::post('student/store-excel-data', 'Admin\StudentController@storeExcelData')->name('storeExcel');
        Route::delete('student/delete/{id}', 'Admin\StudentController@destroy')->name('delete');
        Route::get('student/get-datatables-all-data', 'Admin\StudentController@dataTablesGetAllData')->name('datatablesGetalldata');
    });

    Route::name('course.')->group(function() {
        Route::get('course/index', 'Admin\MapelLmController@index')->name('index');
        Route::get('course/get/{id}', 'Admin\MapelLmController@edit')->name('get');
        Route::post('course/update', 'Admin\MapelLmController@update')->name('update');
        Route::post('course/store', 'Admin\MapelLmController@store')->name('store');
        Route::delete('course/delete/{id}', 'Admin\MapelLmController@destroy')->name('delete');
        Route::get('course/get-datatables-all-data', 'Admin\MapelLmController@dataTablesGetAllData')->name('datatablesGetalldata');
    });

    Route::name('crossInterestClass.')->group(function() {
        Route::get('crossInterestClass/index', 'Admin\LintasMinatClassController@index')->name('index');
        Route::get('crossInterestClass/get/{id}', 'Admin\LintasMinatClassController@edit')->name('get');
        Route::post('crossInterestClass/update', 'Admin\LintasMinatClassController@update')->name('update');
        Route::post('crossInterestClass/store', 'Admin\LintasMinatClassController@store')->name('store');
        Route::delete('crossInterestClass/delete/{id}', 'Admin\LintasMinatClassController@destroy')->name('delete');
        Route::get('crossInterestClass/get-datatables-all-data', 'Admin\LintasMinatClassController@dataTablesGetAllData')->name('datatablesGetalldata');
    });

    Route::name('calresult.')->group(function() {
        Route::get('calresult/index', 'Admin\CalculationResultController@index')->name('index');
        Route::get('calresult/get/{id}', 'Admin\CalculationResultController@edit')->name('get');
        Route::post('calresult/update', 'Admin\CalculationResultController@update')->name('update');
        Route::post('calresult/store', 'Admin\CalculationResultController@store')->name('store');
        Route::delete('calresult/delete/{id}', 'Admin\CalculationResultController@destroy')->name('delete');
        Route::get('calresult/get-datatables-all-data', 'Admin\CalculationResultController@dataTablesGetAllData')->name('datatablesGetalldata');
    });
    // Route::get('users', 'Admin\UserController@index')->name('users');
});