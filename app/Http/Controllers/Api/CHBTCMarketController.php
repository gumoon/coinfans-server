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

class CHBTCMarketController extends Controller
{
    public function ticker() {
        $client = new Client();
        $url = 'http://api.chbtc.com/data/v1/ticker';

        $params = [
            'currency' => 'eth_cny',
        ];
        $res = $client->request('GET', $url, [
            'query' => $params,
        ]);

        $ret = json_decode($res->getBody()->getContents());
        return $this->successJson($ret);
    }
}