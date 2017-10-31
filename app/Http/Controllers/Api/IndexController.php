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
//            echo $e->filter('td')->text();
//            echo $e->filter('td > span')->text();
//            echo $e->filter('td > a')->text();
//            echo $e->filter('.market-cap')->attr('data-usd');
//            echo $e->filter('.market-cap')->attr('data-btc');
//            echo $e->filter('.price')->attr('data-usd');
//            echo $e->filter('.price')->attr('data-btc');
//            echo $e->filter('.volume')->attr('data-usd');
//            echo $e->filter('.volume')->attr('data-btc');
//            echo $e->filter('.circulating-supply').attr('data-supply');
//            echo $e->filter('.positive_change')->attr('data-usd');
            $ee = $e->children();
            foreach($ee as $dd) {
                $c = new Crawler($dd);
                echo $c->text();
            }
            break;
        }

    }
}