<?php
/**
 * Created by PhpStorm.
 * User: gumoon
 * Date: 2017/9/10
 * Time: 14:52
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Symfony\Component\DomCrawler\Crawler;

class IndexController extends Controller
{
    public function index()
    {
        //抓"非小号"网页上的数据
        $html = file_get_contents("/Users/gumoon/Code/coinfans/server/aa.html");

        $crawler = new Crawler($html);
$crawler = $crawler->filter('table > tbody')->children();

        foreach ($crawler as $domElement) {
            $e = new Crawler($domElement);
            $rank = $e->filter('td')->text();
            $symbol = $e->filter('td > span')->text();
            $name = $e->filter('td > a')->text();
            $marketCapUsd = $e->filter('.market-cap')->attr('data-usd');
            $marketCapBtc = $e->filter('.market-cap')->attr('data-btc');
            $priceUsd = $e->filter('.price')->attr('data-usd');
            $priceBtc = $e->filter('.price')->attr('data-btc');
            $volumeUsd = $e->filter('.volume')->attr('data-usd');
            $volumeBtc = $e->filter('.volume')->attr('data-btc');
            //流通量
            $e->filter('.circulating-supply')->attr('data-supply');
            $changeRateUsd = trim($e->filter('.positive_change')->attr('data-usd'), '%');

            $insertData = [
                'rank' => $rank,
            ];
            DB::table('market_timeline')->insert($insertData);
            break;
        }

    }
}