<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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

        var_dump($urls);
    }
}
