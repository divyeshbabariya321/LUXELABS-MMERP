<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Console\Command;

class ImportGiglioImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'giglio:download-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $cookieJar = CookieJar::fromArray([
                '__cfduid' => 'd866a348dc8d8be698f25655b77ada8921560006391',
            ], '.giglio.com');

            $guzzle = new Client;

            $params = [];

            $params['cookies'] = $cookieJar;

            $response = $guzzle->request('GET', 'https://img.giglio.com/images/prodZoom/A66167.001_1.jpg', $params);

            file_put_contents(__DIR__.'/one.jpg', $response->getBody()->getContents());

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
