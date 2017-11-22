<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('fetch:currencyMarketcapOriginData')
                  ->everyFiveMinutes()
                  ->appendOutputTo('/tmp/currency_marketcap_origin_data.log');

         //定时分析市值原始数据
         $schedule->command('post:marketcapTimeline')
             ->everyFiveMinutes()
             ->appendOutputTo('/tmp/marketcap_timeline.log');

         //定时抓取货币下的市场信息
         $schedule->command('fetch:currencyMarketTimeline')
             ->everyFiveMinutes()
             ->appendOutputTo('/tmp/currency_market_timeline.log');

         //定时抓取交易所下的市场信息
         $schedule->command('fetch:exchangeMarketsTimeline')
             ->everyFifteenMinutes()
             ->appendOutputTo('/tmp/exchange_markets_timeline.log');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
