<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class fetchRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抓取汇率信息';

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
        $client = new Client();
        $url = "https://api.fixer.io/latest?base=USD";
        $res = $client->request('GET', $url);
        $ret = $res->getBody()->getContents();

        $ret = json_decode($ret, true);
        var_dump($ret['date'], $ret['base'], json_encode($ret['rates']));

        $insertData = [
            'date' => $ret['date'],
            'base' => $ret['base'],
            'rates' => json_encode($ret['rates']),
        ];
        DB::table('rates')->insert($insertData);
    }
}
