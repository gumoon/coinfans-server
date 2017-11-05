<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchCurrencyMarketCap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:currencyMarketcapOriginData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from coinmarketcap';

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
        //抓取网页，然后入库
        $url = "https://coinmarketcap.com/";
        $html = file_get_contents($url);

        $id = DB::insert('insert into `origin_data` (url, html, add_time) values (?, ?, ?)', [$url, $html, date('Y-m-d H:i:s', time())]);

        $this->comment($id);

        return $id;
    }
}
