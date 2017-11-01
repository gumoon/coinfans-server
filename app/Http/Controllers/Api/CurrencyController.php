<?php
/**
 * Created by PhpStorm.
 * User: gumoon
 * Date: 2017/9/10
 * Time: 14:52
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * 整个市场或某个市场的所有在线币信息
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByMarket(Request $request) {

        $marketId = $request->get('market_id');
        $ret = [
            [
                'rank' => 1,
                'currency_logo' => 'https://files.coinmarketcap.com/static/img/coins/32x32/bitcoin.png',
                'currency_symbol' => 'BTC',
                'currency_name' => 'Bitcoin',
                'market_cap_usd' => '100776285567',
                'market_cap_btc' => '16654512.0',
                'price_usd' => '6050.99',
                'price_btc' => '1.0',
                'volume_usd' => '2488650000.0',
                'volume_btc' => '411263.0',
                'circulating_supply' => '16654512.0',
                'change' => 1, //1=上升，0=下降
                'change_rate_usd' => 3.10,
                'change_rate_btc' => 0.00,
            ],
        ];
        return $this->successJson($ret);
    }

    /**
     * 搜索币
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {
        return $this->successJson();
    }

    /**
     * 24小时交易量最高的20个币
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topByVolume(Request $request) {
        return $this->successJson();
    }
}