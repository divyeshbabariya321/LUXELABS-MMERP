<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\LogRequest;
use App\Setting;
use App\StoreViewsGTMetrix;
use App\WebsiteStoreView;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GTMetrixTestCMD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GT-metrix-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GT metrix test all site';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            Log::info('GTMetrix :: Daily cron start ');
            $cronStatus = Setting::where('name', 'gtmetrixCronStatus')->get()->first();
            if (! empty($cronStatus)) {
                if ($cronStatus->val == 'stop') {
                    Log::info('GTMetrix :: stopped');

                    return false;
                }
            }

            $cronType = Setting::where('name', 'gtmetrixCronType')->get()->first();
            $cronRunTime = Setting::where('name', 'gtmetrixCronRunDate')->get()->first();

            if (! empty($cronRunTime) && ! empty($cronType)) {
                if ($cronRunTime->val != now()->format('Y-m-d') && $cronType->val != 'daily') {
                    Log::info('GTMetrix :: cron run time false');

                    return false;
                }
            }

            if (! empty($cronType)) {
                if ($cronType->val == 'weekly') {
                    $nextDate = now()->addWeeks(1)->format('Y-m-d');
                } else {
                    $nextDate = now()->tomorrow()->format('Y-m-d');
                }
            } else {
                $nextDate = now()->tomorrow()->format('Y-m-d');
            }

            $this->nextCronRunTime($nextDate);
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            Log::info('GTMetrix :: Daily cron start ');
            $storeViewList = WebsiteStoreView::whereNotNull('website_store_id')
                ->join('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')
                ->join('websites as w', 'w.id', 'ws.website_id')
                ->join('store_websites as sw', 'sw.id', 'w.store_website_id')
                ->groupBy('store_website_id')
                ->select('website_store_views.code', 'website_store_views.id', 'sw.website', 'sw.magento_url', 'sw.id as store_website_id')
                ->get()->toArray();

            Log::info('GTMetrix :: store website =>'.count($storeViewList));

            if (! empty($storeViewList)) {
                foreach ($storeViewList as $value) {
                    $webiteUrl = $value['magento_url'];
                    $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                    $curl = curl_init();
                    $url = $webiteUrl.'/pub/sitemap/sitemap_gb_en.xml';

                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => [
                            // Set Here Your Requesred Headers
                            'Content-Type: application/json',
                        ],
                    ]);
                    $response = curl_exec($curl);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    LogRequest::log($startTime, $webiteUrl, 'GET', json_encode([]), json_decode($response), $httpcode, GTMetrixTestCMD::class, 'handle');

                    $err = curl_error($curl);
                    curl_close($curl);

                    if ($err) {
                        Log::info('GTMetrix :: Something went Wrong Not able to fetch sitemap url'.$err);
                        echo 'cURL Error #:'.$err;
                    } else {
                        if (preg_match('/<html[^>]*>/', $response)) {
                            $siteData = [

                                'store_view_id' => $value['id'],
                                'status' => 'not_queued',
                                'website_url' => $webiteUrl.'/'.$value['code'],
                            ];

                            $create[] = $siteData;
                            Log::info("\nMessage:  Not found sitemap data");
                        } elseif (preg_match('/<\?xml[^?]*\?>/', $response)) {
                            Log::info("\nMessage: Site map data fetch succesfully");
                            $xml = simplexml_load_string($response);
                            //Convert into json
                            $xmlToJson = json_encode($xml);
                            // Convert into associative array
                            $finalArray = json_decode($xmlToJson, true);

                            if ($finalArray) {
                                $siteData = [

                                    'store_view_id' => $value['id'],
                                    'status' => 'not_queued',
                                    'website_url' => $webiteUrl.'/'.$value['code'],
                                ];

                                Log::info(print_r($siteData, true));
                                $create[] = $siteData;
                                foreach ($finalArray['url'] as $valueExtra) {
                                    $siteData = [

                                        'store_view_id' => $value['id'],
                                        'status' => 'not_queued',
                                        'website_url' => $valueExtra['loc'],
                                    ];

                                    $create[] = $siteData;
                                }
                            }
                            foreach ($create as $value) {
                                StoreViewsGTMetrix::create($value);
                            }
                        }
                        Log::info('-cUrl:'.json_encode($create)."\nMessage:  Fetch Succesfully");
                    }
                }
            }

            // Get tested site report
            Log::info('GTMetrix :: Daily run complete ');
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            Log::error('GTMetrix :: '.$e->getMessage());

            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    public function nextCronRunTime($date = null)
    {
        $type = Setting::where('name', 'gtmetrixCronRunDate')->get()->first();
        if (empty($type)) {
            $type['name'] = 'gtmetrixCronRunDate';
            $type['type'] = 'date';
            $type['val'] = $date;
            Setting::create($type);
        } else {
            $type->val = $date;
            $type->save();
        }

        return true;
    }
}
