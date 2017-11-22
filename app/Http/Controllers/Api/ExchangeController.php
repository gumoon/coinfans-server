<?php
/**
 *
 * User: gumoon
 * Date: 2017/11/5
 * Time: 12:59
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    /**
     * 搜索交易所
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {
        $keyword = $request->get('keyword');

        $ret = [];
        if(!empty($keyword)) {
            $keyword = '%'.$keyword.'%';
            $ret = DB::select('SELECT * FROM exchanges WHERE short_name like ?', [$keyword]);
        }

        return $this->successJson($ret);
    }

    /**
     * 交易所支持交易的所有货币
     *
     * @param Request $request
     */
    public function coins(Request $request)
    {
        $exchange = $request->get('exchange');

        $ret = [];
        if(!empty($exchange)) {
            //先找到最大的 auto_id
            $maxAutoId = DB::table('exchange_markets_timeline')->max('auto_id');

            //然后找到所有的交易对，分离出来排重就是交易所支持的所有货币了。
            $pairs = DB::table('exchange_markets_timeline')->select('pair')
                ->where('auto_id', $maxAutoId)
                ->where('exchange_short_name', strtolower($exchange))
                ->get();

            foreach ($pairs AS $item) {
                $tmp = explode('/', $item->pair);
                $ret[] = $tmp[0];
            }
        }

        return $this->successJson(array_unique($ret));
    }
}