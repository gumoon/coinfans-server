<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Symfony\Component\DomCrawler\Crawler;

class fetchExchangeMarket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:exchangeActiveMarkets';

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
        //只抓取我们想要的那些交易所，解析出来，插入库
        $exchanges = ['bitfinex','bittrex','poloniex',"bithumb","liqui","kraken","zaif"];

        $client = new Client();
        foreach($exchanges AS $item) {
            $url = "https://coinmarketcap.com/exchanges/".$item.'/';

            $res = $client->request('GET', $url);
            $html = $res->getBody()->getContents();
            $crawler = new Crawler($html);

//            $crawler = $crawler->filter('tbody');
            $childs = $crawler->filter('table')->children();
            $i = 0;
            foreach ($childs as $domElement) {
                if($i++ == 0) {
                    //标题栏
                    continue;
                }
                $insertData = [];
                $insertData['exchange_short_name'] = $item;
                $e = new Crawler($domElement);
                $insertData['rank'] = $e->filter('td')->eq(0)->text();
                $currencyUrl = $e->filter('td')->eq(1)->filter('a')->attr('href');
                $currency = DB::table('currencies')->where('source_url', 'like', '%'.$currencyUrl)->first();

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

                    $currencyId = DB::table('currencies')->insertGetId($insertCurrencyData);
                } else {
                    $currencyId = $currency->id;
                }

                $insertData['currency_id'] = $currencyId;
                $insertData['pair'] = $e->filter('td')->eq(2)->filter('a')->text();
                $insertData['volume_24h'] = trim($e->filter('td')->eq(3)->filter('span')->text());
                echo $e->filter('td')->eq(3)->filter('span')->text()."\n";
                $insertData['price_usd_str'] = trim($e->filter('td')->eq(4)->filter('span')->text());
                $insertData['volume_rate'] = trim($e->filter('td')->eq(5)->text(),'%');
                $insertData['add_time'] = date('Y-m-d H:i:s');

                DB::table('exchange_markets')->insert($insertData);

                var_dump($insertData);
            }
        }
    }
}
