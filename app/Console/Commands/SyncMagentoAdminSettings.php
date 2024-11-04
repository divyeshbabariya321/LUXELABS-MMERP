<?php

namespace App\Console\Commands;

use App\Http\Controllers\MagentoSettingsController;
use App\LogRequest;
use App\MagentoSetting;
use App\StoreWebsite;
use App\Website;
use App\WebsiteStore;
use App\WebsiteStoreView;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncMagentoAdminSettings extends Command
{
    const DATE_FORMATE = 'Y-m-d H:i:s';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento:sync-and-create-config-value {created_by} {sync_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getting All value from magento config and create in magento setting table | Fetch new value by UTC date : YYYY-MM-DD HH:MM:SS';

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
        $startTime = date(self::DATE_FORMATE, LARAVEL_START);
        $sync_type = $this->argument('sync_type');
        $created_by = $this->argument('created_by');

        $api_endpint = '/rest/V1/config/all';
        if (! empty($sync_type) && $sync_type == 'new') {
            $api_endpint .= '/'.now('UTC')->format(self::DATE_FORMATE);
        }

        Log::info('COMMAND >> SyncMagentoAdminSettings::START ['.$startTime.']');

        $storeWebsites = StoreWebsite::whereNotNull('magento_url')->whereNotNull('api_token')->get();

        foreach ($storeWebsites as $storeWebsite) {
            /**
             * store website id = $storeWebsite->id
             */
            Log::info('Store Website magento_url :', [$storeWebsite->magento_url]);

            $websiteUrl = ! empty($storeWebsite->magento_url) ? $storeWebsite->magento_url : '';
            $token = ! empty($storeWebsite->api_token) ? $storeWebsite->api_token : '';
            $curl = curl_init();
            $url = $websiteUrl.$api_endpint;

            Log::info('API >> GET.'.$url.' Request >> START default');

            // Set cURL options
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'content-type: application/json',
                    'Authorization: Bearer '.$token,
                ],
            ]);

            // Get response
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'GET', json_encode(['type' => $sync_type]), json_decode($response), $httpcode, MagentoSettingsController::class, 'handle');

            $response = json_decode($response, true);

            // Log::info("API >> Response >> ",[$response]);

            curl_close($curl);

            if (! empty($response)) {
                $this->storeSyncMagentoSettings($storeWebsite, $response, $created_by);
            }
        }
        Log::info('COMMAND >> SyncMagentoAdminSettings::ENDED ['.date(self::DATE_FORMATE, LARAVEL_START).']');
        $this->info('Command executed successfully!');

        return 0;
    }

    public function storeSyncMagentoSettings($storeWebsite, $response, $created_by)
    {
        foreach ($response as $magento_setting) {
            /**
             *   {
             *       "config_id": 1,
             *       "scope": "default",
             *       "scope_id": 0,
             *       "path": "yotpo/module_info/yotpo_installation_date",
             *       "value": "2020-03-01"
             *   }
             */
            if (! empty($magento_setting['config_id']) && ! empty($magento_setting['scope']) && ! empty($magento_setting['path'])) {

                $scope = $magento_setting['scope'];
                $path = $magento_setting['path'];
                $value = $magento_setting['value'];
                $config_id = $magento_setting['config_id'];
                $scope_id = $magento_setting['scope_id'];
                $website_id = $magento_setting['website_id'];
                $store_view_id = $magento_setting['store_view_id'];

                if ($scope === 'default') {
                    // Log::info("API >> Response Path default >> ",[$magento_setting]);
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $storeWebsite->id)->where('store_website_id', $storeWebsite->id)->where('path', $path)->first();
                    if (empty($m_setting)) {
                        MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $storeWebsite->id,
                            'store_website_id' => $storeWebsite->id,
                            'website_store_id' => 0,
                            'website_store_view_id' => 0,
                            'name' => $path,
                            'path' => $path,
                            'value' => $value,
                            'created_by' => $created_by,
                            'config_id' => $config_id,
                        ]);
                    }
                }

                if ($scope === 'websites' && ! empty($scope_id)) {
                    Log::info('API >> Response Path websites >> ', [$magento_setting]);

                    $websiteStore = WebsiteStore::join('websites as w', 'w.id', 'website_stores.website_id')
                        ->where('w.store_website_id', $storeWebsite->id)
                        ->where('w.platform_id', $website_id)
                        ->select('website_stores.id')
                        ->first();

                    Log::info('API >> Response Path websites websiteStore >> ', [$websiteStore]);

                    if (! empty($websiteStore)) {
                        $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('store_website_id', $storeWebsite->id)->where('path', $path)->first();

                        if (empty($m_setting) && ! empty($websiteStore)) {
                            $m_setting = MagentoSetting::Create([
                                'scope' => $scope,
                                'scope_id' => $websiteStore->id,
                                'store_website_id' => $storeWebsite->id,
                                'website_store_id' => $websiteStore->id,
                                'website_store_view_id' => 0,
                                'name' => $path,
                                'path' => $path,
                                'value' => $value,
                                'created_by' => $created_by,
                                'config_id' => $config_id,
                            ]);

                            Log::info('API >> Response Path m_setting >> CREATED', [$m_setting]);
                        }
                    }
                }

                if ($scope === 'stores' && ! empty($scope_id)) {
                    Log::info('API >> Response Path stores >> ', [$magento_setting]);

                    $fetchStores = WebsiteStore::join('websites as w', 'w.id', 'website_stores.website_id')
                        ->where('w.store_website_id', $storeWebsite->id)
                        ->where('w.platform_id', $website_id)
                        ->select('website_stores.id')
                        ->first();

                    Log::info('API >> Response Path stores fetchStores >> ', [$fetchStores]);

                    if (! empty($fetchStores)) {
                        $websiteStoresView = WebsiteStoreView::where('website_store_id', $fetchStores->id)->where('platform_id', $store_view_id)->first();
                        Log::info('API >> Response Path stores websiteStoresView >> ', [$websiteStoresView]);

                        if (! empty($websiteStoresView)) {
                            $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('store_website_id', $storeWebsite->id)->where('path', $path)->first();
                            if (empty($m_setting)) {
                                MagentoSetting::Create([
                                    'scope' => $scope,
                                    'scope_id' => $websiteStoresView->id,
                                    'store_website_id' => $storeWebsite->id,
                                    'website_store_id' => $websiteStoresView->website_store_id,
                                    'website_store_view_id' => $websiteStoresView->id,
                                    'name' => $path,
                                    'path' => $path,
                                    'value' => $value,
                                    'created_by' => $created_by,
                                    'config_id' => $config_id,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
