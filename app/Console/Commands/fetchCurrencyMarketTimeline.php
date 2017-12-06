<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class fetchCurrencyMarketTimeline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:currencyMarketTimeline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取某个币对应的交易对';

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
        //只抓取我们想要的那些币，解析出来，插入库
        $currencies = ['BTC', 'LTC', 'ETH', "BCH", "ETC", "EOS", "SNT", "XRP", "QTUM"];

        $client = new Client();
        foreach ($currencies AS $item) {
            $currency = DB::table('currencies')->where('symbol', $item)->first();
            $url = $currency->source_url . "#markets";

            $res = $client->request('GET', $url);
            $html = $res->getBody()->getContents();
            $crawler = new Crawler($html);

            $crawler = $crawler->filter('#markets-table > tbody')->children();
            $i = 0;

            $currentTime = date('Y-m-d H:i:s');
            foreach ($crawler as $domElement) {
                $insertData = [];
                $insertData['currency_id'] = $currency->id;
                $e = new Crawler($domElement);
                $insertData['rank'] = $e->filter('td')->eq(0)->text();
                $exchangeUrl = $e->filter('td')->eq(1)->filter('a')->attr('href');
                $searches = ['/exchanges/', '/'];
                $exchangeShortName = str_replace($searches, '', $exchangeUrl);
                $exchange = DB::table('exchanges')->where('short_name', $exchangeShortName)->first();

                //找不到的情况下，插入这个交易所
                if (empty($exchange)) {
                    $insertExchangeData = [];

                    $insertExchangeData['short_name'] = $exchangeShortName;
                    $url = 'https://coinmarketcap.com' . $exchangeUrl;
                    $res = $client->request('GET', $url);

                    $html = $res->getBody()->getContents();
                    $crawler = new Crawler($html);
                    $insertExchangeData['name'] = trim($crawler->filter('h1')->text());

                    $insertExchangeData['website'] = $crawler->filter('.list-unstyled > li')->first()->filter('a')->attr('href');
                    $insertExchangeData['add_time'] = $currentTime;

                    var_dump($insertExchangeData);

                    $exchangeId = DB::table('exchanges')->insertGetId($insertExchangeData);
                } else {
                    $exchangeId = $exchange->id;
                }

                $insertData['exchange_id'] = $exchangeId;
                $insertData['pair'] = trim($e->filter('td')->eq(2)->filter('a')->text());
                $insertData['volume_24h'] = trim($e->filter('td')->eq(3)->filter('span')->text());
                echo $e->filter('td')->eq(3)->filter('span')->text() . "\n";
                $insertData['price_usd_str'] = trim($e->filter('td')->eq(4)->filter('span')->text());
                $insertData['volume_rate'] = trim($e->filter('td')->eq(5)->text(), '%');
                $insertData['add_time'] = $currentTime;

                DB::table('currency_markets_timeline')->insert($insertData);

                var_dump($insertData);

                if ($i >= 50) {
                    echo $item . "满20了";
                    break;
                }

                $i++;
            }
        }
    }
}
