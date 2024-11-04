<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\ScrapApiLog;
use App\Scraper;
use Illuminate\Console\Command;

class ScrapApiLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScrapApi:LogCommand';

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
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        ScrapApiLog::where('created_at', '<', now()->subDays(7))->delete();

        $activeSuppliers = Scraper::with([
            'scraperDuration' => function ($q) {
                $q->orderByDesc('id');
            },
            'scrpRemark' => function ($q) {
                $q->whereNull('scrap_field')->where('user_name', '!=', '')->orderByDesc('created_at');
            },
            'latestMessageNew' => function ($q) {
                $q->whereNotIn('chat_messages.status', ['7', '8', '9', '10'])
                    ->take(1)
                    ->orderByDesc('id');
            },
            'lastErrorFromScrapLogNew',
            'developerTaskNew',
            'scraperMadeBy',
            'childrenScraper.scraperMadeBy',
            'mainSupplier',

        ])
            ->withCount('childrenScraper')
            ->join('suppliers as s', 's.id', 'scrapers.supplier_id')
            ->where('supplier_status_id', 1)
            ->whereIn('scrapper', [1, 2])
            ->whereNull('parent_id')->get();

        foreach ($activeSuppliers as $supplier) {
            $scraper = Scraper::find($supplier->id);
            if (! $scraper->parent_id) {
                $name = $scraper->scraper_name;
            } else {
                $name = $scraper->parent->scraper_name.'/'.$scraper->scraper_name;
            }

            $url = 'http://'.$supplier->server_id.'.theluxuryunlimited.com:'.config('env.NODE_SERVER_PORT').'/send-position?website='.$name;

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $parameters = [];
            LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($response), $httpcode, ScrapApiLogCommand::class, 'handle');

            if (! empty($response)) {
                $response = json_decode($response);

                if (! empty($response->log)) {
                    $log = base64_decode($response->log);

                    if (! empty($log)) {
                        $api_log = new ScrapApiLog;
                        $api_log->scraper_id = $scraper->id;
                        $api_log->server_id = $scraper->server_id;
                        $api_log->log_messages = $log;
                        $api_log->save();
                    }
                }
            }
        }
    }
}
