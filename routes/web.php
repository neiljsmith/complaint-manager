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
Route::get('/', 'ComplaintsController@index')->name('home');

Route::get('/complaints/find/{search}', 'ComplaintsController@findByCustomerAccOrEmail');
Route::get('/complaints/{customer}/create', 'ComplaintsController@create');
Route::post('/complaints/{customer}/store', 'ComplaintsController@store');
Route::post('/complaints/{complaint}/add-note', 'ComplaintsController@addNote');
Route::get('/complaints/{complaint}', 'ComplaintsController@show');

Route::get('/users/num-super-admins', 'UsersController@numSuperAdmins');
Route::get('/users/{user}/test', 'UsersController@test');
Route::get('/users/{user}/superiors', 'UsersController@superiors');
Route::get('/users/{user}/has-subordinates', 'UsersController@hasSubordinates');
Route::get('/users/{id}/duplicate-email/{email}', 'UsersController@duplicateEmail');
Route::resource('users', 'UsersController'); // Generates all default convention CRUD routes

Route::get('/reports', 'ReportsController@stock');
