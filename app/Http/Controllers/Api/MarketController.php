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
use Illuminate\Support\Facades\DB;

class MarketController extends Controller
{
    /**
     * 某种货币的市场列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByCurrency(Request $request) {

        $symbol = strtoupper($request->get('symbol', 'BTC'));

        $coinMarks = DB::select('SELECT
	cm.rank rank,
	e.name name,
	cm.pair pair,
	cm.price_usd_str price_usd_str,
	cm.volume_24h volume_24,
	cm.volume_rate volume_rate,
	cm.add_time update_time 
FROM
	currency_markets_timeline cm
	JOIN currencies c ON cm.currency_id = c.id 
	JOIN exchanges e ON cm.exchange_id = e.id
WHERE
	c.symbol = ? 
ORDER BY
	cm.add_time DESC,
	cm.rank ASC 
	LIMIT 20', [$symbol]);


        return $this->successJson($coinMarks);
    }

    /**
     * 某个交易所提供的市场列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByExchange(Request $request)
    {
        $exchange = $request->get('exchange', 'bitfinex');

        $ret = DB::select('SELECT
	em.rank,
	c.logo,
	c.`name`,
	em.pair,
	em.price_usd_str,
	em.volume_24h,
	em.volume_rate,
	em.add_time update_time 
FROM
	exchange_markets_timeline em
	LEFT JOIN currencies c ON c.id = em.currency_id 
WHERE
	em.exchange_short_name = ? 
ORDER BY
	em.rank ASC', [$exchange]);

        return $this->successJson($ret);
    }
}