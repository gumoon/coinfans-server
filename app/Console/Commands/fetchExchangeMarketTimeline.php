<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Symfony\Component\DomCrawler\Crawler;

class fetchExchangeMarketTimeline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:exchangeMarketsTimeline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '根据交易所获取活跃货币市场';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);
        //只抓取我们想要的那些交易所，解析出来，插入库
//        $exchanges = DB::table('exchanges')->select('short_name')->get();
        $exchanges = ['bitfinex', 'bittrex', 'poloniex', 'bithumb', 'liqui'];

        $client = new Client();
        foreach($exchanges AS $item) {
//            $url = "https://coinmarketcap.com/exchanges/".$item->short_name.'/';
            $url = "https://coinmarketcap.com/exchanges/".$item.'/';
echo $url."\n";
            $res = $client->request('GET', $url);
            $html = $res->getBody()->getContents();
            $crawler = new Crawler($html);
            $childs = $crawler->filter('table')->children();
            $i = 0;
            foreach ($childs as $domElement) {
                if($i++ == 0) {
                    //标题栏
                    continue;
                }
                $insertData = [];
//                $insertData['exchange_short_name'] = $item->short_name;
                $insertData['exchange_short_name'] = $item;
                $e = new Crawler($domElement);
                $insertData['rank'] = $e->filter('td')->eq(0)->text();
                $currencyUrl = $e->filter('td')->eq(1)->filter('a')->attr('href');
echo $currencyUrl."\n";
if($currencyUrl == "/currencies/wowcoin/" || $currencyUrl == '/currencies/safecoin/') {
    continue;
}
                $currency = DB::table('currencies')->where('source_url', 'like', '%'.$currencyUrl)->first();
var_dump($currency);
                //找不到的情况下，插入这个货币
                if(empty($currency)) {
                    $insertCurrencyData = [];

                    $url = 'https://coinmarketcap.com'.$currencyUrl;
                    $insertCurrencyData['source_url'] = $url;
                    $res = $client->request('GET', $url);

                    $html = $res->getBody()->getContents();
                    $crawler = new Crawler($html);
                    $insertCurrencyData['logo'] = $crawler->filter('.currency-logo-32x32')->attr('src');
                    $insertCurrencyData['name'] = $crawler->filter('.currency-logo-32x32')->attr('alt');
                    $symbol = $crawler->filter('h1 > small')->text();
                    $insertCurrencyData['symbol'] = trim($symbol, '()');
                    $insertCurrencyData['website'] = $crawler->filter('.list-unstyled > li')->first()->filter('a')->attr('href');
                    $tags = $crawler->filter('.list-unstyled > li')->last()->html();
                    $insertCurrencyData['mineable'] = strpos($tags, 'Mineable') > 0 ? 1 : 0;
                    $insertCurrencyData['type'] = strpos($tags, 'Coin') > 0 ? 'coin' : 'token';

                    if($currencyUrl == "/currencies/artbyte/") {
                        $currencyId = 224;
                    } elseif($currencyUrl == '/currencies/espers/') {
                        $currencyId = 997;
                    } elseif($currencyUrl = '/currencies/printerium/') {
                        $currencyId = 1053;
                    } else {
                        $currencyId = DB::table('currencies')->insertGetId($insertCurrencyData);
                    }

                } else {
                    $currencyId = $currency->id;
                }

                $insertData['currency_id'] = $currencyId;
                $insertData['pair'] = $e->filter('td')->eq(2)->filter('a')->text();
                $insertData['volume_24h'] = trim($e->filter('td')->eq(3)->filter('span')->text());
                $insertData['price_usd_str'] = trim($e->filter('td')->eq(4)->filter('span')->text());
                $insertData['volume_rate'] = trim($e->filter('td')->eq(5)->text(),'%');
                $insertData['add_time'] = date('Y-m-d H:i:s');
                $insertData['auto_id'] = $_SERVER['REQUEST_TIME'];

                DB::table('exchange_markets_timeline')->insert($insertData);

                var_dump($insertData);
            }
        }
    }
}
