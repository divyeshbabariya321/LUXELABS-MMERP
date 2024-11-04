<?php

namespace App\Console\Commands;

use App\Http\Controllers\MagentoSettingsController;
use App\LogRequest;
use App\MagentoSetting;
use App\StoreWebsite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MagentoConfigValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento:get-config-value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getting value from magento config and update in magento setting table';

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
        Log::info('COMMAND >> MagentoConfigValue::START ['.$startTime.']');

        $magentoSettings = MagentoSetting::leftJoin('users', 'magento_settings.created_by', 'users.id');
        $magentoSettings->select('magento_settings.*', 'users.name as uname');
        $magentoSettings = $magentoSettings->orderByDesc('magento_settings.created_at')->get();

        $data = $magentoSettings;
        $data = $data->groupBy('store_website_id');
        foreach ($data as $websiteId => $settings) {
            $storeWebsite = StoreWebsite::where('id', $websiteId)->first();
            $websiteUrl = ! empty($storeWebsite->magento_url) ? $storeWebsite->magento_url : '';
            $token = ! empty($storeWebsite->api_token) ? $storeWebsite->api_token : '';

            if (! empty($websiteUrl) && ! empty($token)) {
                /**
                 * SYNC Default settings
                 */
                $conf['scopeId'] = 0;
                $conf['scopeType'] = 'default';
                $conf['configs'] = [];
                foreach ($settings as $setting) {
                    if ($setting['scope'] == 'default') {
                        $conf['configs'][] = (object) ['path' => $setting['path']];
                    }
                }
                $curl = curl_init();
                $url = $websiteUrl.'/rest/all/V1/store-info/get-configuration';
                //Log::info("API >> POST." . $url . " Request >> START default");

                // Set cURL options
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 300,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($conf),
                    CURLOPT_HTTPHEADER => [
                        'content-type: application/json',
                        'Authorization: Bearer '.$token,
                    ],
                ]);

                // Get response
                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'POST', json_encode($conf), json_decode($response), $httpcode, MagentoSettingsController::class, 'handle');

                $response = json_decode($response, true);

                curl_close($curl);

                if (! empty($response)) {
                    foreach ($settings as $setting) {
                        foreach ($response as $value) {
                            if (! empty($value['path']) && $setting['path'] == $value['path'] && ! empty($value['value'])) {
                                //Log::info("API >> Response Path default >> " . $value['path'] . " AND Value >> " . $value['value']);

                                MagentoSetting::where('id', $setting['id'])
                                    ->update([
                                        'value_on_magento' => $value['value'],
                                        'value' => $value['value'],
                                    ]);
                            }
                        }
                    }
                }

                /**
                 * SYNC Website settings
                 */
                foreach ($settings as $setting) {
                    if ($setting['scope'] == 'websites') {
                        $conf = [];
                        $conf['scopeId'] = $setting['scope_id'];
                        $conf['scopeType'] = 'websites';
                        $conf['configs'][] = (object) ['path' => $setting['path']];

                        $curl = curl_init();
                        $url = $websiteUrl.'/rest/all/V1/store-info/get-configuration';
                        //Log::info("API >> POST." . $url . " Request >> START websites", [$conf]);

                        // Set cURL options
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 300,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($conf),
                            CURLOPT_HTTPHEADER => [
                                'content-type: application/json',
                                'Authorization: Bearer '.$token,
                            ],
                        ]);

                        // Get response
                        $response = curl_exec($curl);
                        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                        LogRequest::log($startTime, $url, 'POST', json_encode($conf), json_decode($response), $httpcode, MagentoSettingsController::class, 'handle');

                        $response = json_decode($response, true);

                        curl_close($curl);

                        if (! empty($response)) {
                            foreach ($settings as $setting_val) {
                                foreach ($response as $value) {
                                    if (! empty($value['path']) && $setting_val['path'] == $value['path'] && ! empty($value['value'])) {
                                        //Log::info("API >> Response Path websites >> " . $value['path'] . " AND Value >> " . $value['value']);

                                        MagentoSetting::where('id', $setting_val['id'])
                                            ->update([
                                                'value_on_magento' => $value['value'],
                                                'value' => $value['value'],
                                            ]);
                                    }
                                }
                            }
                        }
                    }
                }

                /**
                 * SYNC Stores settings
                 */
                foreach ($settings as $setting) {
                    if ($setting['scope'] == 'stores') {

                        $conf = [];
                        $conf['scopeId'] = $setting['scope_id'];
                        $conf['scopeType'] = 'stores';
                        $conf['configs'][] = (object) ['path' => $setting['path']];

                        $curl = curl_init();
                        $url = $websiteUrl.'/rest/all/V1/store-info/get-configuration';
                        //Log::info("API >> POST." . $url . " Request >> START stores", [$conf]);

                        // Set cURL options
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 300,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($conf),
                            CURLOPT_HTTPHEADER => [
                                'content-type: application/json',
                                'Authorization: Bearer '.$token,
                            ],
                        ]);

                        // Get response
                        $response = curl_exec($curl);
                        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                        LogRequest::log($startTime, $url, 'POST', json_encode($conf), json_decode($response), $httpcode, MagentoSettingsController::class, 'handle');

                        $response = json_decode($response, true);

                        curl_close($curl);

                        if (! empty($response)) {
                            foreach ($settings as $setting_val) {
                                foreach ($response as $value) {
                                    if (! empty($value['path']) && $setting_val['path'] == $value['path'] && ! empty($value['value'])) {
                                        //Log::info("API >> Response Path stores >> " . $value['path'] . " AND Value >> " . $value['value']);

                                        MagentoSetting::where('id', $setting_val['id'])
                                            ->update([
                                                'value_on_magento' => $value['value'],
                                                'value' => $value['value'],
                                            ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        Log::info('COMMAND >> MagentoConfigValue::ENDED ['.date('Y-m-d H:i:s', LARAVEL_START).']');
        $this->info('Command executed successfully!');

        return 0;
    }
}
