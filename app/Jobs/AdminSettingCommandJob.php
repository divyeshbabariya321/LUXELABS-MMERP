<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\StoreWebsite;
use App\MagentoSetting;
use Illuminate\Bus\Queueable;
use App\MagentoSettingPushLog;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AdminSettingCommandJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_selectedCheckboxes;

    protected $_path;

    protected $_value;

    /**
     * Create a new job instance.
     *
     * @param null  $category
     * @param null  $log
     * @param null  $mode
     * @param mixed $selectedCheckboxes
     * @param mixed $path
     * @param mixed $value
     */
    public function __construct($selectedCheckboxes, $path, $value)
    {
        $this->_selectedCheckboxes = $selectedCheckboxes;
        $this->_path               = $path;
        $this->_value              = $value;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Admin setting command');
        foreach ($this->_selectedCheckboxes as $key => $values) {
            $magentoSettings = MagentoSetting::where('id', $values)->first();

            $requestData['command'] = 'bin/magento config:set ' . $this->_path . ' ' . $this->_value;
            Log::info('REquest command ' . $requestData['command']);
            $storeWebsiteData = StoreWebsite::where('id', $magentoSettings->store_website_id)->first();

            if (! empty($storeWebsiteData)) {
                $requestData['server'] = $storeWebsiteData->server_ip;
                $requestData['dir']    = $storeWebsiteData->working_directory;
            }

            if (! empty($requestData['command']) && ! empty($requestData['server']) && ! empty($requestData['dir'])) {
                $requestJson = json_encode($requestData);

                // Initialize cURL session
                $ch = curl_init();

                // Set cURL options for a POST request
                curl_setopt($ch, CURLOPT_URL, 'https://s10.theluxuryunlimited.com:5000/execute');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestJson);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($requestJson),
                ]);

                // Execute cURL session and store the response in a variable
                $response = curl_exec($ch);

                // Check for cURL errors
                if (curl_errno($ch)) {
                    echo 'Curl error: ' . curl_error($ch);
                }

                // Close cURL session
                curl_close($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                $responseData = json_decode($response);
                Log::info('responseData' . print_r($responseData, true));
                $status = 'Error';
                if (isset($responseData->success)) {
                    if ($responseData->success == 1) {
                        $status = 'Success';
                    }
                }

                MagentoSettingPushLog::create(['store_website_id' => $magentoSettings->store_website_id, 'command' => json_encode($requestData), 'setting_id' => $values, 'command_output' => $response, 'status' => $status, 'command_server' => 'http://s10.theluxuryunlimited.com:5000/execute', 'job_id' => $httpcode]);
            }
        }
    }
}
