<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\LogRequest;
use App\StoreGTMetrixAccount;
use App\StoreViewsGTMetrix;
use App\StoreViewsGTMetrixUrl;
use Entrecore\GTMetrixClient\GTMetrixClient;
use Exception;
use Illuminate\Console\Command;

class GTMetrixManageQueueData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:gt_metrix_manage_queue_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GT METRIX MANAGE QUEUE DATA';

    const GTMETRIX_URL = 'https://gtmetrix.com/api/2.0/status';

    const GTMETRIX_UPDATE_MESSAGE = 'Updated GT Matrix view detail by ID:';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron started to run']);

            $gtmatrixAccount = StoreGTMetrixAccount::select('store_gt_metrix_account.*');
            $query = StoreViewsGTMetrixUrl::select('store_views_gt_metrix_url.*');
            $lists = $query->where('process', 1)->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Getting the GT matrix account and url information']);
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);

            if ($lists) {
                foreach ($lists as $list) {
                    $gt_metrix['store_view_id'] = $list->store_view_id;
                    $gt_metrix['account_id'] = $list->account_id;
                    $gt_metrix['website_id'] = $list->id;
                    $gt_metrix['website_url'] = $list->website_url;

                    $new_id = StoreViewsGTMetrix::create($gt_metrix)->id;
                    $gtmetrix = StoreViewsGTMetrix::where('id', $new_id)->first();
                    $gtmatrix = StoreViewsGTMetrix::where('store_view_id', $gt_metrix['store_view_id'])->where('website_id', $gt_metrix['website_id'])->first();

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Saved views GT metrix by ID'.$new_id]);

                    try {
                        if (! empty($gtmatrix->account_id)) {
                            $gtmatrixAccountData = StoreGTMetrixAccount::where('account_id', $gtmatrix->account_id)->first();

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Getting GT Matrix account detail by ID'.$gtmatrix->account_id]);

                            $curl = curl_init();
                            curl_setopt_array($curl, [
                                CURLOPT_URL => self::GTMETRIX_URL,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_USERPWD => $gtmatrixAccountData->account_id.':'.'',
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                            ]);

                            $response = curl_exec($curl);
                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            $url = self::GTMETRIX_URL;
                            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, GTMetrixManageQueueData::class, 'handle');
                            curl_close($curl);
                            $data = json_decode($response);
                            $credits = $data->data->attributes->api_credits;

                            if ($credits != 0) {
                                $client = new GTMetrixClient;
                                $client->setUsername($gtmatrixAccountData->email);
                                $client->setAPIKey($gtmatrixAccountData->account_id);
                                $client->getLocations();
                                $client->getBrowsers();
                                $test = $client->startTest($gtmetrix->website_url);
                                $update = [
                                    'test_id' => $test->getId(),
                                    'status' => 'queued',
                                ];
                                $gtmetrix->update($update);

                                LogHelper::createCustomLogForCron($this->signature, ['message' => self::GTMETRIX_UPDATE_MESSAGE.$gtmetrix->id]);
                            }
                        } else {
                            $AccountData = $gtmatrixAccount->orderByDesc('id')->get();

                            foreach ($AccountData as $value) {
                                $curl = curl_init();
                                $url = self::GTMETRIX_URL;

                                curl_setopt_array($curl, [
                                    CURLOPT_URL => $url,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_USERPWD => $value['account_id'].':'.'',
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'GET',
                                ]);

                                $response = curl_exec($curl);
                                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                                $parameters = [];
                                LogRequest::log($startTime, $url, 'GET', json_encode($parameters), json_decode($response), $httpcode, 'handle', GTMetrixManageQueueData::class);
                                curl_close($curl);

                                // decode the response
                                $data = json_decode($response);
                                $credits = $data->data->attributes->api_credits;
                                if ($credits != 0) {
                                    $client = new GTMetrixClient;
                                    $client->setUsername($value['email']);
                                    $client->setAPIKey($value['account_id']);
                                    $client->getLocations();
                                    $client->getBrowsers();
                                    $test = $client->startTest($gtmetrix->website_url);
                                    $update = [
                                        'test_id' => $test->getId(),
                                        'status' => 'queued',
                                        'account_id' => $value['account_id'],
                                    ];
                                    $gtmetrix->update($update);

                                    LogHelper::createCustomLogForCron($this->signature, ['message' => self::GTMETRIX_UPDATE_MESSAGE.$gtmetrix->id]);
                                    break;
                                }
                            }
                        }
                        Log::info('GTMetrix :: successfully');
                    } catch (Exception $e) {
                        $update = [
                            'test_id' => null,
                            'status' => 'not_queued',
                            'error' => $e->getMessage(),
                        ];
                        $gtmetrix->update($update);

                        LogHelper::createCustomLogForCron($this->signature, ['message' => self::GTMETRIX_UPDATE_MESSAGE.$gtmetrix->id]);
                        Log::info('GTMetrix :: successfully'.$e->getMessage());
                    }
                }
            }
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
