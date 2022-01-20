<?php

use App\Http\Middleware\ApiCheckToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/prueba', function () {
    return 'Prueba desde API';
});

//RUTAS USER
//users
Route::post('/login', 'Api\UserController@login');
Route::post('/register', 'Api\UserController@register');

Route::get('/user/avatar/{filename}', 'Api\UserController@getImage')->middleware('apichecktoken');
Route::post('/profile-update', 'Api\UserController@update')->middleware('apichecktoken');
Route::post('/profile-password-update', 'Api\UserController@updatepassword')->middleware('apichecktoken');
Route::get('/getDecodeToken', 'Api\UserController@getDecodeToken')->middleware('apichecktoken');
//accounts
Route::get('/accounts', 'Api\AccountController@index')->middleware('apichecktoken');
//categories
Route::get('/categories', 'Api\CategoryController@index')->middleware('apichecktoken');
//movements
Route::get('/movements', 'Api\MovementController@index')->middleware('apichecktoken');
Route::get('/movement/{movementId}', 'Api\MovementController@getMovement')->middleware('apichecktoken');
Route::post('/create-movement', 'Api\MovementController@createdMovement')->middleware('apichecktoken');
Route::post('/edit-movement', 'Api\MovementController@editedMovement')->middleware('apichecktoken');
Route::delete('/delete-movement/{movementId}', 'Api\MovementController@deleteMovement')->middleware('apichecktoken');
Route::post('/search-movements', 'Api\MovementController@searchMovements')->middleware('apichecktoken');


//RUTAS ADMIN
//users
Route::get('/admin/all-users', 'Api\UserController@activateUsers')->middleware('apicheckadmin');
Route::put('/admin/activate-user/{userId}', 'Api\UserController@activeUser')->middleware('apicheckadmin');
Route::put('/admin/deactivate-user/{userId}', 'Api\UserController@desactiveUser')->middleware('apicheckadmin');
Route::get('/admin/active-users', 'Api\UserController@listActiveUsers')->middleware('apicheckadmin');
//accounts
Route::get('/admin/all-accounts', 'Api\AccountController@getAllAccounts')->middleware('apicheckadmin');
Route::get('/admin/account/{accountId}', 'Api\AccountController@getAccount')->middleware('apicheckadmin');
Route::put('/admin/activate-account/{accountId}', 'Api\AccountController@activeAccount')->middleware('apicheckadmin');
Route::put('/admin/deactivate-account/{accountId}', 'Api\AccountController@desactiveAccount')->middleware('apicheckadmin');
Route::post('/admin/create-account', 'Api\AccountController@createdAccount')->middleware('apicheckadmin');
Route::post('/admin/edit-account', 'Api\AccountController@editedAccount')->middleware('apicheckadmin');
Route::delete('/admin/delete-account/{accountId}', 'Api\AccountController@deleteAccount')->middleware('apicheckadmin');
//Categories
Route::get('/admin/all-categories', 'Api\CategoryController@getAllCategories')->middleware('apicheckadmin');
Route::get('/admin/category/{categoryId}', 'Api\CategoryController@getCategory')->middleware('apicheckadmin');
Route::put('/admin/activate-category/{categoryId}', 'Api\CategoryController@activeCategory')->middleware('apicheckadmin');
Route::put('/admin/deactivate-category/{categoryId}', 'Api\CategoryController@desactiveCategory')->middleware('apicheckadmin');
Route::post('/admin/create-category', 'Api\CategoryController@createdCategory')->middleware('apicheckadmin');
Route::delete('/admin/delete-category/{categoryId}', 'Api\CategoryController@deleteCategory')->middleware('apicheckadmin');
Route::post('/admin/edit-category', 'Api\CategoryController@editedCategory')->middleware('apicheckadmin');
