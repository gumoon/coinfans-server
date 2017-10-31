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
    public function listByMarket(Request $request) {

        $marketId = $request->get('market_id');
        $ret = [];
        return $this->successJson($ret);
    }
}