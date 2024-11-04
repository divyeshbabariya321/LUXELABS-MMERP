<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\GTMatrixErrorLog;
use App\GTMetrixCategories;
use App\Helpers\LogHelper;
use App\LogRequest;
use App\StoreGTMetrixAccount;
use App\StoreViewsGTMetrix;
use Carbon\Carbon;
use Entrecore\GTMetrixClient\GTMetrixClient;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GTMetrixTestCMDGetReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GT-metrix-test-get-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GT metrix get site report';

    const API_NOT_FOUND = 'API Key not found';

    const API_RESPONSE_ERROR = 'API response error';

    const STORE_VIEW_FOUND = 'Store view found';

    const UPLOAD_GT_MATRIX = '/uploads/gt-matrix/';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            Log::info('GTMetrix :: Report cron start ');

            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            // Get site report
            $storeViewList = StoreViewsGTMetrix::whereNotNull('test_id')
                ->whereNotIn('status', ['completed', 'error', 'not_queued'])
                ->orderByDesc('id')
                ->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'StoreViewsGTMetrix model query was finished']);

            $Api_key = config('settings.pagespeedonline_api_key1');

            foreach ($storeViewList as $value) {
                if ($Api_key == '') {
                    LogHelper::createCustomLogForCron($this->signature, ['message' => self::API_NOT_FOUND]);

                    $this->GTMatrixError($value->id, 'pagespeed', self::API_NOT_FOUND, self::API_NOT_FOUND);
                }
                $curl = curl_init();
                $url = "https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed?url=' . $value->website_url . '&key=' . $Api_key,";
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'Accept: application/json',
                    ],
                ]);

                $response = curl_exec($curl);
                // Get possible error
                $err = curl_error($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, 'handle', GTMetrixTestCMDGetReport::class);
                curl_close($curl);
                if ($err) {
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Getting error from curl request']);

                    $this->GTMatrixError($value->id, 'pagespeed', self::API_RESPONSE_ERROR, $err);
                    Log::info('PageSpeedInsight :: Something went Wrong Not able to fetch site  Result'.$err);
                    echo 'cURL Error #:'.$err;
                } else {
                    Log::info(print_r(['Pagespeed Insight Result started to fetch'], true));
                    $JsonfileName = '/uploads/speed-insight/'.$value->test_id.'_pagespeedInsight.json';
                    $Jsonfile = public_path().$JsonfileName;
                    if (! file_exists($JsonfileName)) {
                        $this->GTMatrixError($value->id, 'pagespeed', 'File not found', $value->test_id.'_pagespeedInsight.json');
                    }
                    file_put_contents($Jsonfile, $response);
                    $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();

                    if (! $storeview) {
                        $this->GTMatrixError($value->id, 'pagespeed', 'Store view test_id', 'store view test_id not found');
                    }

                    Log::info(print_r([self::STORE_VIEW_FOUND], true));

                    if ($storeview) {
                        $storeview->pagespeed_insight_json = $JsonfileName;
                        $storeview->save();

                        if ($response && ! empty($response)) {
                            $responseData = json_decode($response, true);
                            if (isset($responseData) && isset($responseData['lighthouseResult']) && $responseData['lighthouseResult'] != '') {
                                foreach ($responseData['lighthouseResult']['audits'] as $key => $pageSpeedData) {
                                    $key_data = GTMetrixCategories::where('name', $key)
                                        ->where('website_url', '!=', $value->website_url)
                                        ->where('test_id', '!=', $value->test_id)
                                        ->first();
                                    if (! $key_data) {
                                        $GTMetrixCategories = new GTMetrixCategories;
                                        $GTMetrixCategories->name = $key;
                                        $GTMetrixCategories->source = 'Pagespeed Insight';
                                    }
                                }
                            }
                        }
                    }
                }

                if (! empty($value->account_id)) {
                    $gtmatrix = StoreGTMetrixAccount::where('account_id', $value->account_id)->where('status', 'active')->first();

                    $username = $gtmatrix['email'];
                    $password = $gtmatrix['account_id'];

                    $curl = curl_init();
                    $url = 'https://gtmetrix.com/api/2.0/status';

                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_USERPWD => $value->account_id.':'.'',
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                    ]);

                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    if ($err) {
                        $this->GTMatrixError($value->id, 'gtmetrix', self::API_RESPONSE_ERROR, $err);
                    }
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, 'handle', GTMetrixTestCMDGetReport::class);
                    curl_close($curl);
                    $data = json_decode($response);
                    $credits = '';
                    if (isset($data->data->attributes->api_credits)) {
                        $credits = $data->data->attributes->api_credits;
                    }

                    if ($credits != 0) {
                        $client = new GTMetrixClient;
                        $client->setUsername($username);
                        $client->setAPIKey($password);
                        $client->getLocations();
                        $client->getBrowsers();

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Connecting to GTMetrixClient']);
                    } else {
                        $gtmatrixAccount = StoreGTMetrixAccount::select(DB::raw('store_gt_metrix_account.*'));
                        $AccountData = $gtmatrixAccount->where('status', 'active')->orderByDesc('id')->get();

                        foreach ($AccountData as $key => $ValueData) {
                            $curl = curl_init();
                            $url = 'https://gtmetrix.com/api/2.0/status';

                            curl_setopt_array($curl, [
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_USERPWD => $ValueData['account_id'].':'.'',
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                            ]);

                            $response = curl_exec($curl);
                            $err = curl_error($curl);
                            if ($err) {
                                $this->GTMatrixError($value->id, 'gtmetrix', self::API_RESPONSE_ERROR, $err);
                            }
                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, GTMetrixTestCMDGetReport::class, 'handle');
                            curl_close($curl);

                            // decode the response
                            $data = json_decode($response);
                            $credits = $data->data->attributes->api_credits;
                            if ($credits != 0) {
                                $username = $ValueData['email'];
                                $password = $ValueData['account_id'];
                                $client = new GTMetrixClient;
                                $client->setUsername($username);
                                $client->setAPIKey($password);
                                $client->getLocations();
                                $client->getBrowsers();

                                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Connecting to GTMetrixClient']);
                            }
                        }
                    }

                    try {
                        $test = $client->getTestStatus($value->test_id);
                        $value->update([
                            'status' => $test->getState(),
                            'error' => $test->getError(),
                            'report_url' => $test->getReportUrl(),
                            'html_load_time' => $test->getHtmlLoadTime(),
                            'html_bytes' => $test->getHtmlBytes(),
                            'page_load_time' => $test->getPageLoadTime(),
                            'page_bytes' => $test->getPageBytes(),
                            'page_elements' => $test->getPageElements(),
                            'pagespeed_score' => $test->getPagespeedScore(),
                            'yslow_score' => $test->getYslowScore(),
                            'resources' => json_encode($test->getResources()),
                        ]);

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'StoreViewsGTMetrix model update query finished']);

                        $resources = $test->getResources();
                        Log::info(print_r(['Resource started', $resources], true));

                        if (! empty($resources['report_pdf'])) {
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report pdf detail found']);

                            $ch = curl_init($resources['report_pdf']);
                            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                            curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 0);
                            $result = curl_exec($ch);
                            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            $err = curl_error($curl);
                            if ($err) {
                                $this->GTMatrixError($value->id, $resources['report_pdf'], self::API_RESPONSE_ERROR, $err);
                            }
                            curl_close($ch);
                            LogRequest::log($startTime, $resources['report_pdf'], 'GET', json_encode([]), json_decode($response), $statusCode, GTMetrixTestCMDGetReport::class, 'handle');

                            Log::info(print_r(['Result started to fetch'], true));

                            $fileName = self::UPLOAD_GT_MATRIX.$value->test_id.'.pdf';
                            $file = public_path().$fileName;
                            file_put_contents($file, $result);
                            $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();

                            Log::info(print_r([self::STORE_VIEW_FOUND, $storeview], true));

                            if ($storeview) {
                                if (! $storeview) {
                                    $this->GTMatrixError($value->id, 'pagespeed', 'Store view test_id', 'store view test_id not found');
                                }
                            }

                            $storeview->pdf_file = $fileName;
                            $storeview->save();
                        }

                        if (! empty($resources['pagespeed'])) {
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'page speed detail found']);

                            $ch = curl_init($resources['pagespeed']);
                            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                            curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 0);
                            $result = curl_exec($ch);
                            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            $err = curl_error($curl);
                            if ($err) {
                                $this->GTMatrixError($value->id, 'pagespeed', self::API_RESPONSE_ERROR, $err);
                            }
                            curl_close($ch);
                            LogRequest::log($startTime, $resources['pagespeed'], 'GET', json_encode([]), json_decode($response), $statusCode, GTMetrixTestCMDGetReport::class, 'handle');

                            Log::info(print_r(['Result started to fetch pagespeed json'], true));

                            $fileName = self::UPLOAD_GT_MATRIX.$value->test_id.'_pagespeed.json';
                            $file = public_path().$fileName;
                            file_put_contents($file, $result);
                            $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();

                            Log::info(print_r([self::STORE_VIEW_FOUND, $storeview], true));

                            if ($storeview) {
                                $storeview->pagespeed_json = $fileName;
                                $storeview->save();
                                if ($result && ! empty($result)) {
                                    $responseData = json_decode($result, true);
                                    if (isset($responseData) && isset($responseData['rules']) && $responseData['rules'] != '') {
                                        foreach ($responseData['rules'] as $key => $valueData) {
                                            $key_data = GTMetrixCategories::where('name', $key)
                                                ->where('website_url', '!=', $value->website_url)
                                                ->where('test_id', '!=', $value->test_id)
                                                ->first();
                                            if (! $key_data) {
                                                $GTMetrixCategories = new GTMetrixCategories;
                                                $GTMetrixCategories->name = $valueData['name'];
                                                $GTMetrixCategories->source = 'pagespeed';
                                                $GTMetrixCategories->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (! empty($resources['yslow'])) {
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'yslow detail found']);

                            $ch = curl_init($resources['yslow']);
                            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                            curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 0);
                            $result = strip_tags(curl_exec($ch));
                            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            $err = curl_error($curl);
                            if ($err) {
                                $this->GTMatrixError($value->id, 'yslow', self::API_RESPONSE_ERROR, $err);
                            }
                            curl_close($ch);
                            LogRequest::log($startTime, $resources['yslow'], 'GET', json_encode([]), json_decode($response), $statusCode, GTMetrixTestCMDGetReport::class, 'handle');

                            Log::info(print_r(['Result started to fetch yslow json'], true));

                            $fileName = self::UPLOAD_GT_MATRIX.$value->test_id.'_yslow.json';
                            $file = public_path().$fileName;
                            file_put_contents($file, $result);
                            $storeview = StoreViewsGTMetrix::where('test_id', $value->test_id)->where('store_view_id', $value->store_view_id)->first();

                            Log::info(print_r([self::STORE_VIEW_FOUND, $storeview], true));

                            if ($storeview) {
                                $storeview->yslow_json = $fileName;
                                $storeview->save();
                                if ($result && ! empty($result)) {
                                    $responseData = json_decode($result, true);
                                    if (isset($responseData) && isset($responseData['g']) && $responseData['g'] != '') {
                                        foreach ($responseData['g'] as $key => $values) {
                                            $key_data = GTMetrixCategories::where('name', $key)
                                                ->where('website_url', '!=', $value->website_url)
                                                ->where('test_id', '!=', $value->test_id)
                                                ->first();
                                            if (! $key_data) {
                                                $GTMetrixCategories = new GTMetrixCategories;
                                                $GTMetrixCategories->name = $key;
                                                $GTMetrixCategories->source = 'yslow';
                                                $GTMetrixCategories->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Got the error: '.$e->getMessage()]);

                        $value->status = 'error';
                        $value->error = $e->getMessage();
                        $value->save();
                        if ($err) {
                            $this->GTMatrixError($value->id, 'pagespeed', 'catch error', $e->getMessage());
                        }
                    }
                } else {
                    if (empty($value->account_id)) {
                        $this->GTMatrixError($value->id, 'pagespeed', 'Store view Account_id', 'store view Account_id not found');
                    }
                }
            }
            Log::info('GTMetrix :: Report cron complete ');
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    public function GTMatrixError($store_viewGTM_id, $erro_type, $error_title, $error = '')
    {
        try {
            $GTError = new GTMatrixErrorLog;
            $GTError->store_viewGTM_id = $store_viewGTM_id;
            $GTError->error_type = $erro_type;
            $GTError->error_title = $error_title;
            $GTError->error = $error;
            $GTError->save();
        } catch (Exception $e) {
            $GTError = new GTMatrixErrorLog;
            $GTError->store_viewGTM_id = $store_viewGTM_id;
            $GTError->error_type = $erro_type;
            $GTError->error = $e->getMessage();
            $GTError->save();
        }
    }
}
