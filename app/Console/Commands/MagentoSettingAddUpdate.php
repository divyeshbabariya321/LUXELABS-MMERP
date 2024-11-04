<?php

namespace App\Console\Commands;

use App\CronJob;
use App\LogRequest;
use App\MagentoSetting;
use App\MagentoSettingUpdateResponseLog;
use App\StoreWebsite;
use Exception;
use Illuminate\Console\Command;

class MagentoSettingAddUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoSettingUpdates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento setting Updates';

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
            $websites = StoreWebsite::whereNotNull('api_token')->whereNotNull('magento_url')->whereNotNull('server_ip')->get();
            foreach ($websites as $website) {
                if ($website->api_token != '' && $website->server_ip != '') {
                    $curl = curl_init();
                    $url = $website->magento_url.'/rest/V1/core/config/';
                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => [
                            'authorization:Bearer '.$website->api_token,
                        ],
                    ]);
                    $response = curl_exec($curl);
                    if (curl_errno($curl)) {
                        curl_error($curl);
                    }

                    $response = curl_exec($curl);
                    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    $resArr = is_string($response) ? json_decode($response, true) : $response;
                    LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $http_code, MagentoSettingAddUpdate::class, 'handle');
                    curl_close($curl);

                    if (is_array($resArr) && isset($resArr[0][0]) && $resArr[0][0]['config_id']) {
                        foreach ($resArr as $res1) {
                            foreach ($res1 as $res) {
                                if (isset($res['config_id'])) {
                                    $findMegntoSetting = MagentoSetting::where('store_website_id', $website->id)->where('path', $res['path'])->get();
                                    if (! empty($findMegntoSetting->toArray())) {
                                        foreach ($findMegntoSetting as $megntoSetting) {
                                            MagentoSetting::where('id', $megntoSetting->id)->update(
                                                [
                                                    'config_id' => $res['config_id'],
                                                    'scope' => $res['scope'],
                                                    'store_website_id' => $website->id,
                                                    'website_store_id' => $website->id,
                                                    'scope_id' => $res['scope_id'],
                                                    'path' => $res['path'],
                                                    'value' => $res['value'],
                                                    'updated_at' => $res['updated_at'],
                                                ]
                                            );
                                        }
                                    } else {
                                        MagentoSetting::create(
                                            [
                                                'config_id' => $res['config_id'],
                                                'scope' => $res['scope'],
                                                'store_website_id' => $website->id,
                                                'website_store_id' => $website->id,
                                                'scope_id' => $res['scope_id'],
                                                'path' => $res['path'],
                                                'value' => $res['value'],
                                                'updated_at' => $res['updated_at'],
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                        MagentoSettingUpdateResponseLog::create(
                            [
                                'website_id' => $website->id,
                                'response' => is_array($response) ? json_encode($response) : $response,
                            ]
                        );
                    } else {
                        MagentoSettingUpdateResponseLog::create(
                            [
                                'website_id' => $website->id,
                                'response' => is_array($response) ? json_encode($response) : $response,
                            ]
                        );
                    }
                } else {
                    $token = empty($website->api_token) ? 'Please Check API TOKEN' : '';
                    $server_ip = empty($website->server_ip) ? ' Please Check Server Ip' : '';
                    MagentoSettingUpdateResponseLog::create(
                        [
                            'website_id' => $website->id,
                            'response' => $token.', ==  '.$server_ip,
                        ]
                    );
                }// end if condition if api_tocken not found
            } //end website foreach
        } catch (Exception $e) {
            MagentoSettingUpdateResponseLog::create(
                [
                    'website_id' => $website->id,
                    'magento_setting_id' => $findMegntoSetting[0]->id ?? '',
                    'response' => is_array($e->getMessage()) ? json_encode($e->getMessage()) : $e->getMessage(),
                ]
            );
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
