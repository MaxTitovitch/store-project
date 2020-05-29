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


Route::post('register', 'API\RegisterController@register');
Route::post('login', 'API\RegisterController@login');
Route::post('reset/send', 'API\RegisterController@sendResetLinkEmail');
Route::post('reset/save', 'API\RegisterController@reset');
Route::post('verify/send', 'API\RegisterController@verifyResend')->middleware('api-auth:false');
Route::post('verify/save', 'API\RegisterController@verifySave');

Route::middleware('api-auth:true')->group( function () {
    Route::resource('tags', 'API\TagController');

});