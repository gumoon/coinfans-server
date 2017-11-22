<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class fetchCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抓取货币信息';

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
        //先抓取首页上所有货币的链接，然后再去对应的页面抓取
        $originData = DB::select('select * from `origin_data` where id > 0 and status = 1 order by id limit 1');
        $urls = [];
        foreach($originData AS $item) {
            $crawler = new Crawler($item->html);
            $crawler = $crawler->filter('table > tbody')->children();

            foreach ($crawler as $domElement) {
                $e = new Crawler($domElement);
                $urls[] = $e->filter('.currency-symbol > a')->attr('href');
            }
        }
        unset($originData);

        $client = new Client();
        foreach($urls AS $url) {
            $insertCurrencyData = [];

            $url = 'https://coinmarketcap.com'.$url;
            $insertCurrencyData['source_url'] = $url;
            $res = $client->request('GET', $url);

            $html = $res->getBody()->getContents();
            $crawler = new Crawler($html);
            $insertCurrencyData['logo'] = trim($crawler->filter('.currency-logo-32x32')->attr('src'));
            $insertCurrencyData['name'] = trim($crawler->filter('.currency-logo-32x32')->attr('alt'));
            $symbol = $crawler->filter('h1 > small')->text();
            $insertCurrencyData['symbol'] = trim($symbol, '()');
            $insertCurrencyData['website'] = $crawler->filter('.list-unstyled > li')->first()->filter('a')->attr('href');
            $tags = $crawler->filter('.list-unstyled > li')->last()->html();
            $insertCurrencyData['mineable'] = strpos($tags, 'Mineable') > 0 ? 1 : 0;
            $insertCurrencyData['type'] = strpos($tags, 'Coin') > 0 ? 'coin' : 'token';

            DB::table('currencies')->insert($insertCurrencyData);

            var_dump($insertCurrencyData);
        }

    }
}
