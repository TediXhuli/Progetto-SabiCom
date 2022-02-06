<?php

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

Route::post('login', 'AuthController@login')
    ->name('api.login');

Route::get('activities', 'ActivitiesController@index'); //get all
Route::get('activities/{id}', 'ActivitiesController@show'); //read one

Route::post('activities', 'ActivitiesController@store'); //create
Route::patch('activities/{id}', 'ActivitiesController@update'); //update
Route::delete('activities/{id}', 'ActivitiesController@destroy');//delete