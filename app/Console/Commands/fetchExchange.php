<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Psy\Exception\ErrorException;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\DB;

class fetchExchange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:exchange';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抓取交易所信息';

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
        //先抓取首页上所有交易所的链接，然后再去对应的页面抓取
        $client = new Client();
        $urls = [];
//        $marketUrl = "https://coinmarketcap.com/currencies/bitcoin/#markets";
        $marketUrl = "https://coinmarketcap.com/currencies/eos/#markets";
        $html = $client->request('GET', $marketUrl);
        $crawler = new Crawler($html->getBody()->getContents());
        $crawler = $crawler->filter('#markets-table > tbody')->children();

        foreach ($crawler as $domElement) {
            $e = new Crawler($domElement);
            $urls[] = $e->filter('a')->attr('href');
        }

        unset($html, $crawler);


        foreach($urls AS $url) {
            $insertData = [];

            $searches = ['/exchanges/', '/'];
            $insertData['short_name'] = str_replace($searches, '', $url);
            $url = 'https://coinmarketcap.com'.$url;
            $res = $client->request('GET', $url);

            $html = $res->getBody()->getContents();
            $crawler = new Crawler($html);
            $insertData['name'] = $crawler->filter('h1')->text();

            $insertData['website'] = $crawler->filter('.list-unstyled > li')->first()->filter('a')->attr('href');
            $insertData['add_time'] = date('Y-m-d H:i:s');

            $exchange = DB::table('exchanges')->where('short_name', $insertData['short_name'])->first();
            if(empty($exchange)) {
                DB::table('exchanges')->insert($insertData);
            }

//
            var_dump($insertData);
        }
    }
}
