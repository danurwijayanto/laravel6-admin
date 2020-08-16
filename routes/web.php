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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Route::name('admin.')->group(function () {
//     Route::get('users', 'Admin\UserController@index')->name('users');

// });

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function(){
    Route::name('user.')->group(function () {
        Route::get('index', 'Admin\UserController@index')->name('index');
        Route::get('get-datatables-all-data', 'Admin\UserController@dataTablesGetAllData')->name('datatablesGetalldata');
    
    });
    // Route::get('users', 'Admin\UserController@index')->name('users');
});