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

Route::post('register', 'API\RegisterController@register');
Route::post('login', 'API\RegisterController@login');
Route::post('reset/send', 'API\RegisterController@sendResetLinkEmail');
Route::post('reset/save', 'API\RegisterController@reset');
Route::post('verify/send', 'API\RegisterController@verifySend')->middleware('api-auth:false,false,user');
Route::post('verify/save', 'API\RegisterController@verifySave');

Route::middleware('api-auth:true,false,user')->group( function () {
    Route::put('users/update-user/{id}', 'API\UserController@updateUser');
    Route::delete('users/delete-user/{id}', 'API\UserController@destroyUser');

    Route::post('rankings/store-user/{id}', 'API\RankingController@storeUser');
    Route::put('rankings/update-user/{id}', 'API\RankingController@updateUser');
    Route::delete('rankings/delete-user/{id}', 'API\RankingController@destroyUser');

    Route::post('orders/store-user/{id}', 'API\OrderController@storeUser');
    Route::put('orders/update-user/{id}', 'API\OrderController@updateUser');

    Route::post('likes/store-user/{id}', 'API\LikeController@storeUser');
    Route::delete('likes/delete-user/{id}', 'API\LikeController@destroyUser');

    Route::post('comments/store-user/{id}', 'API\CommentController@storeUser');
    Route::delete('comments/delete-user/{id}', 'API\CommentController@destroyUser');

    Route::get('addresses/index-user', 'API\AddressController@indexUser');
    Route::get('addresses/show-user/{id}', 'API\AddressController@showUser');
    Route::post('addresses/store-user/{id}', 'API\AddressController@storeUser');
    Route::put('addresses/update-user/{id}', 'API\AddressController@updateUser');
    Route::delete('addresses/delete-user/{id}', 'API\AddressController@destroyUser');

    Route::post('photo/user-image/{id}', 'API\PhotoController@createUserPhoto');

});

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

    Route::get('line-chart/{entity}/{id}/{param}', 'API\DiagramController@showLineChart');
    Route::get('bar-chart/{entity}/{param}', 'API\DiagramController@showBarChart');
    Route::get('pie-chart/{param}', 'API\DiagramController@showPieChart');
});

Route::middleware('api-auth:true,true,admin')->group( function () {
    Route::resource('addresses', 'API\AddressController')->except(['create', 'edit']);
    Route::resource('orders', 'API\OrderController')->except(['create', 'edit']);

    Route::post('photo', 'API\PhotoController@createPhoto');
});
