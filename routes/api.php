<?php


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

Route::Post('/register', 'Auth\AuthController@register');
Route::Post('/login', 'Auth\AuthController@login');
//Route::get('/me', 'Auth\AuthController@user');
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('/me', 'Auth\AuthController@user');
    Route::Post('/logout', 'Auth\AuthController@logout');
});