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

Route::get('/user-request', 'ProductController@userRequest')->name('product.request');
Route::get('/{requestID}/destroy', 'ProductController@destroy')->name('product.destroy');
Route::get('/{requestID}/list', 'ProductController@list')->name('product.list');
Route::get('/{requestID}/detail', 'ProductController@detail')->name('product.detail');


Route::get('notification', 'DiscordNotification@notification');

Route::get('/migrate', function(){
	\Artisan::call("migrate");
});