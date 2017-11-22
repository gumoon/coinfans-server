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
        $exchanges = $request->get('exchanges');

//        $coinMarks = DB::select('SELECT
//	cm.rank rank,
//	e.name name,
//	cm.pair pair,
//	cm.price_usd_str price_usd_str,
//	cm.volume_24h volume_24,
//	cm.volume_rate volume_rate,
//	cm.add_time update_time
//FROM
//	currency_markets_timeline cm
//	JOIN currencies c ON cm.currency_id = c.id
//	JOIN exchanges e ON cm.exchange_id = e.id
//WHERE
//	c.symbol = ? AND e.short_name IN (?)
//ORDER BY
//	cm.add_time DESC,
//	cm.rank ASC
//	LIMIT 20', [$symbol, $exchanges]);
        $maxTime = DB::table('currency_markets_timeline')->where('pair', 'like', $symbol.'%' )->max('add_time');

        $query = DB::table('currency_markets_timeline')
                        ->join('currencies', 'currency_markets_timeline.currency_id', '=', 'currencies.id')
                        ->join('exchanges', 'currency_markets_timeline.exchange_id', '=', 'exchanges.id')
                        ->select('currency_markets_timeline.rank', 'exchanges.short_name', 'exchanges.name',
                            'currency_markets_timeline.pair', 'currency_markets_timeline.price_usd_str',
                            'currency_markets_timeline.volume_24h', 'currency_markets_timeline.volume_rate', 'currency_markets_timeline.add_time as update_time')
                        ->where('currencies.symbol', $symbol)
                        ->where('currency_markets_timeline.add_time', $maxTime);

        if(!empty($exchanges)) {
            $query->whereIn('exchanges.short_name', explode(',', $exchanges));
        }

        $coinMarks = $query->orderBy('currency_markets_timeline.rank', 'asc')
            ->limit(20)
            ->get();


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
        $currencies = $request->get('currencies');

        $exchange = strtolower($exchange);
        $maxAutoId = DB::table('exchange_markets_timeline')->max('auto_id');

        $query = DB::table('exchange_markets_timeline')
                    ->leftJoin('currencies', 'currencies.id', '=', 'exchange_markets_timeline.currency_id')
                    ->select('exchange_markets_timeline.rank', 'currencies.logo', 'currencies.name', 'exchange_markets_timeline.pair',
                        'exchange_markets_timeline.price_usd_str', 'exchange_markets_timeline.volume_24h', 'exchange_markets_timeline.volume_rate',
                        'exchange_markets_timeline.add_time as update_time')
                    ->where('exchange_markets_timeline.exchange_short_name', $exchange)
                    ->where('exchange_markets_timeline.auto_id', $maxAutoId);

        if(!empty($currencies)) {
            $query->whereIn('currencies.symbol', explode(',', $currencies));
        }

        $ret = $query->orderBy('exchange_markets_timeline.auto_id', 'desc')
                    ->limit(20)
                    ->get();

        //        $ret = DB::select('SELECT
//	emt.rank,
//	c.logo,
//	c.`name`,
//	emt.pair,
//	emt.price_usd_str,
//	emt.volume_24h,
//	emt.volume_rate,
//	emt.add_time update_time
//FROM
//	exchange_markets_timeline emt
//	LEFT JOIN currencies c ON c.id = emt.currency_id
//WHERE
//	emt.exchange_short_name = ? and emt.auto_id = (SELECT max(emt.auto_id) FROM exchange_markets_timeline)
//ORDER BY
//	emt.auto_id desc, emt.rank ASC limit 0, 20', [strtolower($exchange)]);

        return $this->successJson($ret);
    }
}