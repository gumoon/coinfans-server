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
     * 支持某个币交易的所有市场列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByCoin(Request $request) {

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
	coin_markets cm
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
     * 搜索市场
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {
        return $this->successJson();
    }
}