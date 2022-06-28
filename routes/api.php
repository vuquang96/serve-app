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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post('buff/update', 'BuffController@update');
    
    Route::post('csgoroll/update', 'CsgorollController@update');
    
    Route::group(['namespace' => 'ExportMarket'], function () {
        Route::post('market/empty', 'MarketController@emptyTable');
        Route::get('market/list/inventory', 'MarketController@listInventory');
        Route::post('market/inventory', 'MarketController@inventory');
        Route::post('market/buff', 'MarketController@buff');
        Route::post('market/csgoempire', 'MarketController@csgoempire');
        Route::post('market/csgoroll', 'MarketController@csgoroll');
        Route::post('market/csgoroll-default', 'MarketController@csgorollDefault');
        Route::get('market/download-data', 'MarketController@downloadData');
        Route::get('market/check-ready', 'MarketController@checkReady');
    });


    Route::group(['namespace' => 'CbExportMarket'], function () {
        Route::get('cb/market/list/inventory', 'CbMarketController@listInventory');
        Route::post('cb/market/buff', 'CbMarketController@buff');
        Route::post('cb/market/csgoroll', 'CbMarketController@csgoroll');

        Route::get('cb/market/download-data', 'CbMarketController@downloadData');
        Route::get('cb/market/test', 'CbMarketController@setInventory');
    });
});

