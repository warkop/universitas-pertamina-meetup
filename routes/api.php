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

Route::group(['prefix' => 'title'], function () {
    Route::get('/', 'TitleController@index');
    Route::get('/{title}', 'TitleController@show');
    Route::post('/', 'TitleController@store');
    Route::put('/{title}', 'TitleController@store');
    Route::delete('/{title}', 'TitleController@destroy');
});

Route::group(['prefix' => 'nationality'], function () {
    Route::get('/', 'NationalityController@index');
    Route::get('/{nationality}', 'NationalityController@show');
    Route::post('/', 'NationalityController@store');
    Route::put('/{nationality}', 'NationalityController@store');
    Route::delete('/{nationality}', 'NationalityController@destroy');
});

Route::group(['prefix' => 'skill'], function () {
    Route::get('/', 'SkillController@index');
    Route::get('/{skill}', 'SkillController@show');
    Route::post('/', 'SkillController@store');
    Route::put('/{skill}', 'SkillController@store');
    Route::delete('/{skill}', 'SkillController@destroy');
});

