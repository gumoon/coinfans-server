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

class CoinController extends Controller
{
    /**
     * 某个市场的所有在线币信息
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByMarket(Request $request) {

        $marketId = $request->get('market_id');
        $ret = [];
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