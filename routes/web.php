<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Route::get('/home', 'HomeController@index')->name('home')->middleware('checkuseractive');

//RUTAS DE USUARIO
Route::get('/profile', 'UserController@profile')->name('user.profile')->middleware('checkuseractive');
Route::post('/profile-update', 'UserController@update')->name('user.update')->middleware('checkuseractive');
Route::get('/profile-password', 'UserController@password')->name('user.password')->middleware('checkuseractive');
Route::post('/profile-password-update', 'UserController@updatepassword')->name('user.updatepassword')->middleware('checkuseractive');
Route::get('/user/avatar/{filename}', 'UserController@getImage')->name('user.avatar');

//RUTAS ADMIN
//users
Route::get('/admin/activate-users', 'UserController@activateUsers')->name('admin.activateusers')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/active-user/{userId}', 'UserController@activeUser')->name('admin.activeuser')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/desactive-user/{userId}', 'UserController@desactiveUser')->name('admin.desactiveuser')->middleware('checkuseractive','checkuseradmin');

//accounts
Route::get('/admin/accounts', 'AccountController@index')->name('admin.accounts')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/active-account/{accountId}', 'AccountController@activeAccount')->name('admin.activeaccount')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/desactive-account/{accountId}', 'AccountController@desactiveAccount')->name('admin.desactiveaccount')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/create-account', 'AccountController@createAccount')->name('admin.createaccount')->middleware('checkuseractive','checkuseradmin');
Route::post('/admin/created-account', 'AccountController@createdAccount')->name('admin.createdaccount')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/edit-account/{accountId}', 'AccountController@editAccount')->name('admin.editaccount')->middleware('checkuseractive','checkuseradmin');
Route::post('/admin/edited-account', 'AccountController@editedAccount')->name('admin.editedaccount')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/delete-account/{accountId}', 'AccountController@deleteAccount')->name('admin.deleteaccount')->middleware('checkuseractive','checkuseradmin');

//categories
Route::get('/admin/categories', 'CategoryController@index')->name('admin.categories')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/active-category/{categoryId}', 'CategoryController@activeCategory')->name('admin.activecategory')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/desactive-category/{categoryId}', 'CategoryController@desactiveCategory')->name('admin.desactivecategory')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/create-category', 'CategoryController@createCategory')->name('admin.createcategory')->middleware('checkuseractive','checkuseradmin');
Route::post('/admin/created-category', 'CategoryController@createdCategory')->name('admin.createdcategory')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/edit-category/{categoryId}', 'CategoryController@editCategory')->name('admin.editcategory')->middleware('checkuseractive','checkuseradmin');
Route::post('/admin/edited-category', 'CategoryController@editedCategory')->name('admin.editedcategory')->middleware('checkuseractive','checkuseradmin');
Route::get('/admin/delete-category/{categoryId}', 'CategoryController@deleteCategory')->name('admin.deletecategory')->middleware('checkuseractive','checkuseradmin');

//movements
Route::get('/movements', 'MovementController@index')->name('movements')->middleware('checkuseractive');
Route::post('/movements/search', 'MovementController@search')->name('searchmovements')->middleware('checkuseractive');
Route::get('/movement/ticket/{filename}', 'MovementController@getTicket')->name('movement.ticket');

Route::get('/create-movement', 'MovementController@createMovement')->name('createmovement')->middleware('checkuseractive');
Route::post('/created-movement', 'MovementController@createdMovement')->name('createdmovement')->middleware('checkuseractive');
Route::get('/edit-movement/{movementId}', 'MovementController@editMovement')->name('editmovement')->middleware('checkuseractive');
Route::post('/edited-movement', 'MovementController@editedMovement')->name('editedmovement')->middleware('checkuseractive');
Route::get('/delete-movement/{movementId}', 'MovementController@deleteMovement')->name('deletemovement')->middleware('checkuseractive');
