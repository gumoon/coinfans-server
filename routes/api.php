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

Route::get('/huobi/market/detail', 'HuobiMarketController@detail');
Route::get('/bithumb/public/ticker', 'BithumbPublicController@ticker');
Route::get('/chbtc/market/ticker', 'CHBTCMarketController@ticker');
Route::get('/index/index', 'IndexController@index');

Route::get('/coin/listByExchange', 'CurrencyController@listByExchange');
Route::get('/coin/search', 'CurrencyController@search');
Route::get('/coin/topByMarketCap', 'CurrencyController@topByMarketCap');

Route::get('/market/listByCoin', 'MarketController@listByCoin');
Route::get('/market/search', 'MarketController@search');
