<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\DomCrawler\Crawler;

class PostTimeline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:marketTimeline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'post a market timeline';

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
        //从 origin_data 表中取出1000条记录，按正序排列，确保先处理id小的。
        //循环分析这1000条记录，写入 market_timeline 表，然后，更改 origin_data 表字段为已分析。

        $originData = DB::select('select * from `origin_data` where id > 0 and status = 0 order by id limit 10');
        foreach($originData AS $item) {
            $crawler = new Crawler($item['html']);
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

                $changeRateUsd = trim($e->filter('.percent-24h')->attr('data-usd'), '%');
                $changeRateBtc = trim($e->filter('.percent-24h')->attr('data-btc'), '%');

                $insertData = [
                    'or_id' => $item['id'],
                    'rank' => $rank,
                    'symbol' => $symbol,
                    'name' => $name,
                    'price_btc' => $priceBtc,
                    'price_usd' => $priceUsd,
                    'volume_usd' => $volumeUsd,
                    'volume_btc' => $volumeBtc,
                    'market_cap_usd' => $marketCapUsd,
                    'market_cap_btc' => $marketCapBtc,
                    'change_rate_usd' => $changeRateUsd,
                    'change_rate_btc' => $changeRateBtc,
                    'created_at' => date('Y-m-d H:i:s'),
                    'publish_at' => $item['add_time']
                ];

                DB::beginTransaction();
                DB::table('market_timeline')->insert($insertData);
                //更新 origin_data 为解析成功
                DB::table('origin_data')->where('id', $item['id'])->update(['status' => 1]);
                DB::commit();
            }
        }
    }
}
