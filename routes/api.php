<?php

use Illuminate\Http\Request;

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


Route::post('user/login', 'UserController@login');
Route::post('user/register', 'UserController@register');

Route::middleware('auth:api')->group(function () {
    Route::get('user/user', 'UserController@details');
});

Route::post('login', 'PassportController@login');
Route::post('register', 'PassportController@register');
 
Route::middleware('auth:admin')->group(function () {
    Route::get('user', 'PassportController@details');
});


