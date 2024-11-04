<?php

namespace App\Console\Commands;

use App\CronJob;
use App\LogRequest;
use App\NegativeCouponResponse;
use App\StoreWebsite;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class NegativeCouponResponses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:NegativeCouponResponses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Negative Coupon Response';

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

            $storeWebsites = StoreWebsite::select('store_websites.id', 'store_websites.api_token', 'store_websites.website')->where('api_token', '!=', '')->where('website_source', 'magento')->get();
            foreach ($storeWebsites as $storeWebsite) {
                $authorization = 'Authorization: Bearer '.$storeWebsite->api_token;
                // Init cURL
                $curl = curl_init();
                $url = "'https://dev6.sololuxury.com/rest/V1/coupon/logs/'";
                // Set cURL options
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 300,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_HTTPHEADER => [
                        'content-type: application/json',
                        $authorization,
                    ],
                ]);

                // Get response
                $response = curl_exec($curl);

                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($response), $httpcode, NegativeCouponResponses::class, 'handle');

                // Get possible error
                $err = curl_error($curl);

                // Close cURL
                curl_close($curl);

                // Check for errors
                if ($err) {
                    //
                }
                $convertJson = is_array($response) ? json_encode($response) : $response;
                $user_id = Auth::user()->id ?? '';
                NegativeCouponResponse::create(
                    [
                        'store_website_id' => $storeWebsite->id,
                        'user_id' => $user_id,
                        'website' => $storeWebsite->website,
                        'response' => $convertJson,
                    ]
                );
            }
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
