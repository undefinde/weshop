<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'ShopController@index');
Route::get('goods/{goods_id}', 'ShopController@goods');
Route::get('buy/{goods_id}', 'ShopController@buy');
Route::get('cart', 'ShopController@cart');
Route::get('clear', 'ShopController@clear');
Route::post('submit_order', 'ShopController@submit_order');
Route::any('weshop', 'WeshopController@index');
Route::get('test', 'WeshopController@test');
Route::get('login', 'UserController@login');
Route::get('logout', 'UserController@logout');
