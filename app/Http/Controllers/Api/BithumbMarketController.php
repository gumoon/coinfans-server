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

class BithumbMarketController extends Controller
{
    public function ticker() {
        $client = new Client();
        $url = 'https://api.bithumb.com/public/ticker/eth';

        $res = $client->request('GET', $url);

        $ret = json_decode($res->getBody()->getContents());
        return $this->successJson($ret);
    }
}