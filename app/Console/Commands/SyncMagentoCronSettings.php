<?php

namespace App\Console\Commands;

use App\Http\Controllers\MagentoSettingsController;
use App\LogRequest;
use App\MagentoSetting;
use App\StoreWebsite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncMagentoCronSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento:sync-and-create-cron-config-value {created_by}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getting All cronjob value from magento config and create in magento setting table';

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
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $created_by = $this->argument('created_by');

        $api_endpint = '/rest/V1/cronmanager/jobs';

        Log::info('COMMAND >> SyncMagentoCronSettings::START ['.$startTime.']');

        $storeWebsites = StoreWebsite::select('id', 'magento_url', 'api_token')->whereNotNull('magento_url')->whereNotNull('api_token')->get();

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
            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, MagentoSettingsController::class, 'handle');

            $response = json_decode($response, true);

            //            Log::info("API >> Response >> ",[$response]);

            curl_close($curl);

            if (! empty($response)) {
                $this->storeSyncCronSettings($storeWebsite, $response, $created_by);
            }
        }
        Log::info('COMMAND >> SyncMagentoCronSettings::ENDED ['.date('Y-m-d H:i:s', LARAVEL_START).']');
        $this->info('Command executed successfully!');

        return 0;
    }

    public function storeSyncCronSettings($storeWebsite, $response, $created_by)
    {
        foreach ($response as $all_magento_setting) {
            foreach ($all_magento_setting as $magento_setting) {
                /**
                 *   array:4 [
                 *       "name" => "aggregate_sales_report_bestsellers_data"
                 *       "instance" => "Magento\Sales\Model\CronJob\AggregateSalesReportBestsellersData"
                 *       "method" => "execute"
                 *       "schedule" => "0 0 * * *"
                 *   ]
                 */
                if (! empty($magento_setting['name']) && ! empty($magento_setting['method'])) {
                    $scope = $magento_setting['method'];
                    $name = $magento_setting['name'];
                    $instance = $magento_setting['instance'];
                    $value = ! empty($magento_setting['schedule']) ? $magento_setting['schedule'] : '';

                    if ($scope === 'execute') {
                        //                        Log::info("API >> Response Path default >> ",[$magento_setting]);
                        $m_setting = MagentoSetting::where('scope', $scope)
                            ->where('scope_id', $storeWebsite->id)
                            ->where('store_website_id', $storeWebsite->id)
                            ->where('name', $name)
                            ->where('path', $instance)
                            ->first();

                        if (empty($m_setting)) {
                            MagentoSetting::Create([
                                'scope' => $scope,
                                'scope_id' => $storeWebsite->id,
                                'store_website_id' => $storeWebsite->id,
                                'website_store_id' => 0,
                                'website_store_view_id' => 0,
                                'name' => $name,
                                'path' => $instance,
                                'value' => $value,
                                'created_by' => $created_by,
                                'config_type' => 'cronjob',
                            ]);
                        }
                    }
                }
            }
        }
    }
}
