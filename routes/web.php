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
        Route::get('user/get-datatables-all-data', 'Admin\UserController@dataTablesGetAllData')->name('datatablesGetalldata');
    
    });

    Route::name('roles.')->group(function() {
        Route::get('roles/index', 'Admin\RoleController@index')->name('index');
    });
    // Route::get('users', 'Admin\UserController@index')->name('users');
});