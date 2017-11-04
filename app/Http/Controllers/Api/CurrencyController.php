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

class CurrencyController extends Controller
{
    /**
     * 整个市场或某个市场的所有在线币信息
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
	exchange_markets em
	LEFT JOIN currencies c ON c.id = em.currency_id 
WHERE
	em.exchange_short_name = ? 
ORDER BY
	em.rank ASC', [$exchange]);

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
    public function topByMarketCap(Request $request) {

        $limit = $request->get('limit', 20);

        $ret = [];
        $totalMarketTimeline = DB::select('select * from `market_timeline` mt join currencies c on mt.symbol = c.symbol where or_id = 
             (select max(or_id) from `market_timeline`) order by rank limit ?', [$limit]);
        foreach($totalMarketTimeline AS $item) {
            $ret[] = [
                'rank' => $item->rank,
                'currency_logo' => $item->logo,
                'currency_symbol' => $item->symbol,
                'currency_name' => $item->name,
                'market_cap_usd' => $item->market_cap_usd,
                'market_cap_btc' => $item->market_cap_btc,
                'price_usd' => $item->price_usd,
                'price_btc' => $item->price_btc,
                'price_cny' => round($item->price_usd * 6.7, 2),
                'volume_usd' => $item->volume_usd,
                'volume_btc' => $item->volume_btc,
                'change_rate_usd' => $item->change_rate_usd,
                'change_rate_btc' => $item->change_rate_btc,
                'publish_at' => $item->publish_at,
            ];
        }

        return $this->successJson($ret);
    }
}