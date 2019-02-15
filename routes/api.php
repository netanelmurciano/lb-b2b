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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['cors']], function () {
    
    Route::get('products', 'ProductsController@index');

    //Route::post('order', 'OrderController@store');

    Route::post('order', 'CustomerOrderInfoController@create');

    Route::post('login', 'CustomersController@login');

    Route::post('register', 'CustomersController@register');

    Route::post('checkEmail', 'CustomersController@customerByEmail');

    ///////////////// ADMIN ROUTES ///////////////////
    Route::post('addProduct', 'ProductsController@create');

    Route::post('removeProduct', 'ProductsController@removeProduct');

    Route::post('updateProduct', 'ProductsController@updateProduct');
});