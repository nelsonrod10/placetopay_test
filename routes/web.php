<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();

Route::resource('products', 'ProductsController');
Route::get('create-order/{product}', 'OrdersController@create');
Route::resource('orders', 'OrdersController');
Route::get('validate-payment/{reference}', 'PlacetoPlayController@validateStatus');
Route::post('payment-result', 'PlacetoPlayController@show')->name('payment-result');

