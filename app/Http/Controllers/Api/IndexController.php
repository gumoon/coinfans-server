<?php
/**
 * Created by PhpStorm.
 * User: gumoon
 * Date: 2017/9/10
 * Time: 14:52
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class IndexController extends Controller
{
    public function test() {
        $client = new Client();
        $url = 'https://be.huobi.com/v1/common/currencys';

        $params = [
            'symbol' => 'ethcny',
        ];
        $res = $client->request('GET', $url, [
            'query' => $params,
        ]);

        $ret = json_decode($res->getBody()->getContents());
        return $this->successJson($ret);
    }
}