<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\LogRequest;
use App\StoreWebsite;
use App\MagentoSetting;
use Illuminate\Bus\Queueable;
use App\MagentoSettingPushLog;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class PushMagentoCronSettings implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public $tries = 5;
    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param protected $magentoSetting
     * @param protected $website_ids
     *
     * @return void
     */
    public function __construct(protected $magentoSetting, protected $website_ids) {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void {
        Log::info('PushMagentoCronSettings Queue');
        try {
            // Set time limit
            set_time_limit(0);

            // Load product and website
            $magentoSetting = $this->magentoSetting;
            $entity = $magentoSetting;

            $scope = $entity->scope;
            $name = $entity->name;
            $path = $entity->path;
            $value = $entity->value;
            $datatype = $entity->datatype;
            $website_ids = $this->website_ids;

            Log::info('website_ids : ' . print_r($website_ids, true));
            Log::info('Setting Scope : ' . $scope);

            $storeWebsites = StoreWebsite::whereIn('id', $website_ids ?? [])->get();
            foreach ($storeWebsites as $storeWebsite) {
                $store_website_id = $storeWebsite->id;
                $storeWebsiteCode = $storeWebsite->storeCode;

                Log::info('Start Setting Pushed to : ' . $store_website_id);

                $api_token = $storeWebsite->api_token;
                $magento_url = $storeWebsite->magento_url;

                $m_setting = MagentoSetting::where('scope', $scope)
                        ->where('scope_id', $store_website_id)
                        ->where('store_website_id', $store_website_id)
                        ->where('path', $path)
                        ->where('name', $name)
                        ->first();
                if (!$m_setting) {
                    $m_setting = MagentoSetting::Create([
                                'scope' => $scope,
                                'scope_id' => $store_website_id,
                                'name' => $name,
                                'path' => $path,
                                'value' => $value,
                                'data_type' => $datatype,
                                'config_type' => "execute",
                    ]);
                } else {
                    $m_setting->name = $name;
                    $m_setting->path = $path;
                    $m_setting->value = $value;
                    $m_setting->data_type = $datatype;
                    $m_setting->save();
                }

                $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                if (isset($storeWebsiteCode->code)) {
                    $url = rtrim($magento_url, '/') . '/' . $storeWebsiteCode->code . '/rest/V1/cron-expression/update';
                } else {
                    $url = rtrim($magento_url, '/') . '/rest/V1/cron-expression/update';
                }

                $data = [];
                $data['jobCode'] = $name;
                $data['frequency'] = $value;

                Log::info('API CALL ' . $url . ' : ', [$data]);

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $api_token]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $result = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                Log::info(print_r([json_encode($data), $url, $result], true));

                LogRequest::log($startTime, $url, 'PUT', json_encode($data), json_decode($result), $httpcode, PushMagentoCronSettings::class, 'handle');

                if (curl_errno($ch)) {
                    Log::info('API Error: ' . curl_error($ch));
                    MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $m_setting->id, 'command_output' => curl_error($ch), 'status' => 'Error', 'command_server' => $url, 'job_id' => $httpcode]);
                }

                $response = json_decode($result);
                curl_close($ch);
                if ($httpcode == '200') {
                    $m_setting->status = 'Success';
                    $m_setting->save();
                    MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $m_setting->id, 'command_output' => $result, 'status' => 'Success', 'command_server' => $url, 'job_id' => $httpcode]);
                } else {
                    $m_setting->status = 'Error';
                    $m_setting->save();
                    MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $m_setting->id, 'command_output' => $result, 'status' => 'Error', 'command_server' => $url, 'job_id' => $httpcode]);
                }
                Log::info('End Setting Pushed to : ' . $store_website_id);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function tags() {
        return ['pushMagentoCronSettings', $this->magentoSetting->id];
    }
}
