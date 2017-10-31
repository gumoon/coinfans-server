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

class MarketController extends Controller
{
    /**
     * 支持某个币交易的所有市场列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByCoin(Request $request) {

        return $this->successJson();
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