<?php

namespace App\Console\Commands;

use App\HashTag;
use App\LogRequest;
use Illuminate\Console\Command;

class RunPriorityKeywordSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:priority-keyword-search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run priority keyword search - normal, affiliate';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //call priority api call for google search
        // $searchKeywords = HashTag::where('priority', 1)->where('platforms_id', 2)->get(['hashtag', 'id']);
        $searchKeywords = [];
        $postData = json_encode($searchKeywords);

        // call this endpoint - /api/googleSearch
        $this->callCurl(config('settings.node_scraper_server').'api/googleSearch', $postData);

        //call priority api call for google affiliate search
        $affiliateKeywords = HashTag::where('priority', 1)->where('platforms_id', 3)->get(['hashtag', 'id']);

        // $affiliateKeywords = [];
        $postData = json_encode($affiliateKeywords);

        // call this endpoint - /api/googleSearchDetails
        $this->callCurl(config('settings.node_scraper_server').'api/googleSearchDetails', $postData);
    }

    public function callCurl($url, $postData)
    {
        // call this endpoint
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => "$postData",
        ]);
        $response = curl_exec($curl);

        $err = curl_error($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($postData), json_decode($response), $httpcode, RunPriorityKeywordSearch::class, 'callCurl');
        curl_close($curl);
    }
}
