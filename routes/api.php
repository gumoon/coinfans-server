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

//货币
Route::get('/currency/search', 'CurrencyController@search');
Route::get('/currency/topByMarketCap', 'CurrencyController@topByMarketCap');

//市场
Route::get('/market/listByExchange', 'CurrencyController@listByExchange');
Route::get('/market/listByCurrency', 'MarketController@listByCurrency');

//交易所
Route::get('/exchange/search', 'MarketController@search');
