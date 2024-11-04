<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class TryScrappingFromFarfetchIfAttributesAreMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'farfetch:pull-details-which-are-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

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

            $content = $this->getDetailsFromFarfetch('');
            $c = new HtmlPageCrawler($content);
            $data = $c->filter('._659731 div p._87b3a2')->getInnerHtml();

            dd($data);

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    public function getDetailsFromFarfetch($url)
    {
        $request = new Client;
        $response = $request->get('https://www.farfetch.com/ae/shopping/men/balenciaga-rhino-t-shirt-item-13445516.aspx?storeid=10952');

        return $response->getBody()->getContents();
    }
}
