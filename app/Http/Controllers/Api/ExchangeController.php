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
}