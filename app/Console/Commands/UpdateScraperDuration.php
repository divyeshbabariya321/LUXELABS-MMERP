<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\Scraper;
use App\ScraperDuration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateScraperDuration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateScraperDuration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'UpdateScraperDuration';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
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
            ->whereNull('parent_id')
            ->orderByDesc('scrapers.flag')
            ->orderBy('s.supplier')
            ->get();

        foreach ($activeSuppliers as $scraper) {
            if ($scraper->server_id) {
                if (! $scraper->parent_id) {
                    $name = $scraper->scraper_name;
                } else {
                    $name = $scraper->parent->scraper_name.'/'.$scraper->scraper_name;
                }

                /* This curl need to replace with guzzleHttp but for now i am keeping this. */

                $url = 'http://'.$scraper->server_id.'.theluxuryunlimited.com:'.config('env.NODE_SERVER_PORT').'/process-list?filename='.$name.'.js';

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($response), $httpcode, UpdateScraperDuration::class, 'handle');
                curl_close($curl);
                $duration = json_decode($response);

                if (empty($duration->Process[0])) {
                    Log::debug('Scrapper Duration Log: => '.$response);

                    continue;
                }

                $pid = $duration->Process[0]->pid;
                $duration = isset($duration->Process[0]->duration) ? $duration->Process[0]->duration : null;

                if ($duration) {
                    $duration = explode(' ', $duration);
                    $text = '';
                    if (in_array('Hours', $duration)) {
                        $text .= (strlen($duration[0]) == 2 ? $duration[0] : '0'.$duration[0]).':';
                        $text .= $duration[0].':';
                    } else {
                        $text .= '00:';
                    }
                    if (in_array('Miuntes', $duration)) {
                        $text .= (strlen($duration[array_search('Miuntes', $duration) - 1]) == 2 ? $duration[array_search('Miuntes', $duration) - 1] : '0'.$duration[array_search('Miuntes', $duration) - 1]).':';
                    } else {
                        $text .= '00:';
                    }
                    if (in_array('Seconds', $duration)) {
                        $text .= (strlen($duration[array_search('Seconds', $duration) - 1]) == 2 ? $duration[array_search('Seconds', $duration) - 1] : '0'.$duration[array_search('Seconds', $duration) - 1]);
                    } else {
                        $text .= '00';
                    }

                    $scrap_duration = ScraperDuration::where('scraper_id', $scraper->id)->where('process_id', $pid)->first();
                    if (! $scrap_duration) {
                        $scrap_duration = new ScraperDuration;
                    }
                    $scrap_duration->scraper_id = $scraper->id;
                    $scrap_duration->process_id = $pid;
                    $scrap_duration->duration = $text;
                    $scrap_duration->save();
                }
            }
        }
    }
}
