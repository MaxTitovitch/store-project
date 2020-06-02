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
    Route::resource('categories', 'API\CategoryController')->except(['create', 'edit']);
    Route::resource('characteristics', 'API\CharacteristicController')->except(['create', 'edit']);
    Route::resource('characteristic-values', 'API\CharacteristicValueController')->except(['create', 'edit']);
    Route::resource('comments', 'API\CommentController')->except(['create', 'edit']);
    Route::resource('likes', 'API\LikeController')->except(['create', 'edit']);
    Route::resource('views', 'API\ViewController')->except(['create', 'edit']);
    Route::resource('posts', 'API\PostController')->except(['create', 'edit']);
    Route::resource('products', 'API\ProductController')->except(['create', 'edit']);
    Route::resource('product-characteristics', 'API\ProductCharacteristicController')->except(['create', 'edit']);
    Route::resource('rankings', 'API\RankingController')->except(['create', 'edit']);
    Route::resource('sales', 'API\SaleController')->except(['create', 'edit']);
    Route::resource('sale-categories', 'API\SaleCategoryController')->except(['create', 'edit']);
    Route::resource('users', 'API\UserController')->except(['create', 'edit']);
    Route::resource('tops', 'API\TopController')->except(['create', 'edit']);

    Route::resource('category-characteristic', 'API\CategoryCharacteristicController')->only(['index', 'store', 'destroy']);
    Route::resource('product-top', 'API\ProductTopController')->only(['index', 'store', 'destroy']);
    Route::resource('product-tag', 'API\ProductTagController')->only(['index', 'store', 'destroy']);
    Route::resource('product-order', 'API\ProductOrderController')->only(['index', 'store', 'destroy']);

});

Route::middleware('api-auth:true,true,admin')->group( function () {
    Route::resource('addresses', 'API\AddressController')->except(['create', 'edit']);
    Route::resource('orders', 'API\OrderController')->except(['create', 'edit']);

});

Route::middleware('api-auth:true,false,user')->group( function () {
    Route::get('users/update-user', 'API\UserController@updateUser');
    Route::get('users/delete-user', 'API\UserController@destroyUser');

    Route::get('rankings/store-user', 'API\RankingController@storeUser');
    Route::get('rankings/update-user', 'API\RankingController@updateUser');
    Route::get('rankings/delete-user', 'API\RankingController@destroyUser');

    Route::get('orders/store-user', 'API\OrderController@storeUser');
    Route::get('orders/update-user', 'API\OrderController@updateUser');

    Route::get('likes/store-user', 'API\LikeController@storeUser');
    Route::get('likes/delete-user', 'API\LikeController@destroyUser');

    Route::get('comments/store-user', 'API\CommentController@storeUser');
    Route::get('comments/delete-user', 'API\CommentController@destroyUser');

    Route::get('addresses/index-user', 'API\AddressController@indexUser');
    Route::get('addresses/show-user', 'API\AddressController@showUser');
    Route::get('addresses/store-user', 'API\AddressController@storeUser');
    Route::get('addresses/update-user', 'API\AddressController@updateUser');
    Route::get('addresses/delete-user', 'API\AddressController@destroyUser');

});