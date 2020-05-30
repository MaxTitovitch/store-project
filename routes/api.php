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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::post('register', 'API\RegisterController@register');
Route::post('login', 'API\RegisterController@login');
Route::post('reset/send', 'API\RegisterController@sendResetLinkEmail');
Route::post('reset/save', 'API\RegisterController@reset');
Route::post('verify/send', 'API\RegisterController@verifySend')->middleware('api-auth:false,false,user');
Route::post('verify/save', 'API\RegisterController@verifySave');

Route::middleware('api-auth:true,false,admin')->group( function () {
    Route::resource('tags', 'API\TagController')->except(['create', 'edit']);
    Route::resource('addresses', 'API\AddressController')->except(['create', 'edit']);
    Route::resource('categories', 'API\CategoryController')->except(['create', 'edit']);
    Route::resource('characteristics', 'API\CharacteristicController')->except(['create', 'edit']);
    Route::resource('characteristic-values', 'API\CharacteristicValueController')->except(['create', 'edit']);

});