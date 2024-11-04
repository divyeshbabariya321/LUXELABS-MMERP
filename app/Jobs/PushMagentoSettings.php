<?php

namespace App\Jobs;
use Illuminate\Support\Facades\Log;
use App\LogRequest;
use App\StoreWebsite;
use App\WebsiteStore;
use App\MagentoSetting;
use App\WebsiteStoreView;
use Illuminate\Bus\Queueable;
use App\MagentoSettingPushLog;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class PushMagentoSettings implements ShouldQueue {

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
        Log::info('PushMagentoSettings Queue');
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
            // #DEVTASK-23677-api implement for admin settings
            Log::info('Setting Scope : ' . $scope);
            // Scope Default
            if ($scope === 'default') {
                $storeWebsites = StoreWebsite::whereIn('id', $website_ids ?? [])->get();
                foreach ($storeWebsites as $storeWebsite) {
                    $store_website_id = $storeWebsite->id;
                    $storeWebsiteCode = $storeWebsite->storeCode;
                    Log::info('Start Setting Pushed to : ' . $store_website_id);
                    $api_token = $storeWebsite->api_token;
                    $magento_url = $storeWebsite->magento_url;

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $store_website_id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                                    'scope' => $scope,
                                    'scope_id' => $store_website_id,
                                    'name' => $name,
                                    'path' => $path,
                                    'value' => $value,
                                    'data_type' => $datatype,
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
                        $url = rtrim($magento_url, '/') . '/' . $storeWebsiteCode->code . '/rest/all/V1/store-info/configuration';
                    } else {
                        $url = rtrim($magento_url, '/') . '/rest/all/V1/store-info/configuration';
                    }

                    $data = [];
                    $data['scopeId'] = 0;
                    $data['scopeType'] = 'default';
                    $data['configs'][] = ['path' => $path, 'value' => $value];

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $api_token]);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    $result = curl_exec($ch);
                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    Log::info(print_r([json_encode($data), $url, $result], true));

                    LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($result), $httpcode, PushMagentoSettings::class, 'handle');

                    if (curl_errno($ch)) {
                        Log::info('API Error: ' . curl_error($ch));
                        MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $m_setting->id, 'command_output' => curl_error($ch), 'status' => 'Error', 'command_server' => $url, 'job_id' => $httpcode]);
                    }

                    $response = json_decode($result);
                    curl_close($ch);
                    if ($httpcode == '200') {
                        $m_setting->status = 'Success';
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $m_setting->id, 'command_output' => 'Success', 'status' => 'Success', 'command_server' => $url, 'job_id' => $httpcode]);
                    } else {
                        $m_setting->status = 'Error';
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $m_setting->id, 'command_output' => $result, 'status' => 'Error', 'command_server' => $url, 'job_id' => $httpcode]);
                    }
                    Log::info('End Setting Pushed to : ' . $store_website_id);
                }
            }
            // Scope Default
            if ($scope === 'websites') {
                $store = isset($entity->store->website->name) ? $entity->store->website->name : '';
                Log::info('Setting Pushed to store : ' . $store);

                $websiteStores = WebsiteStore::with('website.storeWebsite')->whereHas('website', function ($q) use ($store, $website_ids) {
                    $q->whereIn('store_website_id', $website_ids ?? [])->where('name', $store);
                        })->orWhere('id', $entity->scope_id)->get();

                foreach ($websiteStores as $websiteStore) {
                    $store_website_id = isset($websiteStore->website->storeWebsite->id) ? $websiteStore->website->storeWebsite->id : 0;

                    $storeWebsiteCode = isset($websiteStore->website->storeWebsite->storeCode) ? $websiteStore->website->storeWebsite->storeCode : 0;

                    Log::info('Start Setting Pushed to Website Store : ' . $websiteStore->id);
                    Log::info('store_website_id : ' . $store_website_id);

                    $magento_url = isset($websiteStore->website->storeWebsite->magento_url) ? $websiteStore->website->storeWebsite->magento_url : null;
                    $api_token = isset($websiteStore->website->storeWebsite->api_token) ? $websiteStore->website->storeWebsite->api_token : null;

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                                    'scope' => $scope,
                                    'scope_id' => $websiteStore->id,
                                    'name' => $name,
                                    'path' => $path,
                                    'value' => $value,
                                    'data_type' => $datatype,
                        ]);
                    } else {
                        $m_setting->name = $name;
                        $m_setting->path = $path;
                        $m_setting->value = $value;
                        $m_setting->data_type = $datatype;
                        $m_setting->save();
                    }
                    $scopeID = $websiteStore->platform_id;
                    if (!empty($magento_url) && !empty($api_token)) {
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

                        if (isset($storeWebsiteCode->code)) {
                            $url = rtrim($magento_url, '/') . '/' . $storeWebsiteCode->code . '/rest/all/V1/store-info/configuration';
                        } else {
                            $url = rtrim($magento_url, '/') . '/rest/all/V1/store-info/configuration';
                        }

                        $data = [];
                        $data['scopeId'] = $scopeID;
                        $data['scopeType'] = 'websites';
                        $data['configs'][] = ['path' => $path, 'value' => $value];

                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $api_token]);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        $result = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        Log::info(print_r([json_encode($data), $url, $result], true));

                        LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($result), $httpcode, PushMagentoSettings::class, 'handle');

                        if (curl_errno($ch)) {
                            Log::info('API Error: ' . curl_error($ch));
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' => curl_error($ch), 'status' => 'Error', 'command_server' => $url, 'job_id' => $httpcode]);
                        }

                        $response = json_decode($result);
                        curl_close($ch);
                        if ($httpcode == '200') {
                            $m_setting->status = 'Success';
                            $m_setting->save();
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' => 'Success', 'status' => 'Success', 'command_server' => $url, 'job_id' => $httpcode]);
                        } else {
                            $m_setting->status = 'Error';
                            $m_setting->save();
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' => $result, 'status' => 'Error', 'command_server' => $url, 'job_id' => $httpcode]);
                        }
                    } else {
                        $m_setting->status = 'Error';
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => '', 'setting_id' => $m_setting->id, 'command_output' => 'Magento URL & API Token is not found', 'status' => 'Error', 'job_id' => '500']);
                    }
                    Log::info('End Setting Pushed to Website Store : ' . $websiteStore->id);
                }
            }
            // Scope Default
            if ($scope === 'stores') {
                $store = isset($entity->storeview->websiteStore->website->name) ? $entity->storeview->websiteStore->website->name : '';
                $store_view = isset($entity->storeview->code) ? $entity->storeview->code : '';
                Log::info('Setting Pushed to store : ' . $store);
                Log::info('Setting Pushed to  store_view: ' . $store_view);

                $websiteStoresViews = WebsiteStoreView::with('websiteStore.website.storeWebsite')->whereHas('websiteStore.website', function ($q) use ($store, $website_ids) {
                            $q->where('name', $store)->whereIn('store_website_id', $website_ids ?? []);
                        })->where('code', $store_view)->orWhere('id', $entity->scope_id)->get();

                foreach ($websiteStoresViews as $websiteStoresView) {
                    $store_website_id = isset($websiteStoresView->websiteStore->website->storeWebsite->id) ? $websiteStoresView->websiteStore->website->storeWebsite->id : 0;

                    $storeWebsiteCode = isset($websiteStoresView->websiteStore->website->storeWebsite->storeCode) ? $websiteStoresView->websiteStore->website->storeWebsite->storeCode : 0;

                    Log::info('Start Setting Pushed to Website Store View: ' . $websiteStoresView->id);
                    Log::info('store_website_id : ' . $store_website_id);

                    $magento_url = isset($websiteStoresView->websiteStore->website->storeWebsite->magento_url) ? $websiteStoresView->websiteStore->website->storeWebsite->magento_url : null;
                    $api_token = isset($websiteStoresView->websiteStore->website->storeWebsite->api_token) ? $websiteStoresView->websiteStore->website->storeWebsite->api_token : null;

                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                    if (!$m_setting) {
                        $m_setting = MagentoSetting::Create([
                                    'scope' => $scope,
                                    'scope_id' => $websiteStoresView->id,
                                    'name' => $name,
                                    'path' => $path,
                                    'value' => $value,
                                    'data_type' => $datatype,
                        ]);
                    } else {
                        $m_setting->name = $name;
                        $m_setting->path = $path;
                        $m_setting->value = $value;
                        $m_setting->data_type = $datatype;
                        $m_setting->save();
                    }
                    $scopeID = $websiteStoresView->platform_id;
                    if (!empty($magento_url) && !empty($api_token)) {
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

                        if (isset($storeWebsiteCode->code)) {
                            $url = rtrim($magento_url, '/') . '/' . $storeWebsiteCode->code . '/rest/all/V1/store-info/configuration';
                        } else {
                            $url = rtrim($magento_url, '/') . '/rest/all/V1/store-info/configuration';
                        }

                        $data = [];
                        $data['scopeId'] = $scopeID;
                        $data['scopeType'] = 'stores';
                        $data['configs'][] = ['path' => $path, 'value' => $value];

                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $api_token]);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        $result = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        Log::info(print_r([json_encode($data), $url, $result], true));

                        LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($result), $httpcode, PushMagentoSettings::class, 'handle');

                        if (curl_errno($ch)) {
                            Log::info('API Error: ' . curl_error($ch));
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' => curl_error($ch), 'status' => 'Error', 'command_server' => $url, 'job_id' => $httpcode]);
                        }

                        $response = json_decode($result);
                        curl_close($ch);
                        if ($httpcode == '200') {
                            $m_setting->status = 'Success';
                            $m_setting->save();
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' => 'Success', 'status' => 'Success', 'command_server' => $url, 'job_id' => $httpcode]);
                        } else {
                            $m_setting->status = 'Error';
                            $m_setting->save();
                            MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => json_encode($data), 'setting_id' => $entity->id, 'command_output' => $result, 'status' => 'Error', 'command_server' => $url, 'job_id' => $httpcode]);
                        }
                    } else {
                        $m_setting->status = 'Error';
                        $m_setting->save();
                        MagentoSettingPushLog::create(['store_website_id' => $store_website_id, 'command' => '', 'setting_id' => $m_setting->id, 'command_output' => 'Magento URL & API Token is not found', 'status' => 'Error', 'job_id' => '500']);
                    }
                    Log::info('End Setting Pushed to Website Store View: ' . $websiteStoresView->id);
                }
            }
            // #DEVTASK-23677-api implement for admin settings
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function tags() {
        return ['pushMagentoSettings', $this->magentoSetting->id];
    }
}
