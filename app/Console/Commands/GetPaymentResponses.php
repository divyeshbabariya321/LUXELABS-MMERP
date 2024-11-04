<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\LogRequest;
use App\PaymentResponse;
use App\StoreWebsite;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GetPaymentResponses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:payment-responses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Websites Payment Responses';

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
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            $websites = StoreWebsite::whereNotNull('magento_url')->whereNotNull('api_token')->get();
            $date = Carbon::now()->format('Y-m-d');
            foreach ($websites as $web) {
                $token = $web->api_token;
                $web_url = strtolower($web->magento_url);
                $url = parse_url($web_url);
                if (! isset($url['scheme'])) {
                    $web_url = 'https://'.$web_url;
                }
                $api = "$web_url/rest/V1/payment/sales/order/$date";
                $data = json_decode($this->getDataApi($api, $token), true);
                if (! empty($data) && count($data) > 0) {
                    foreach ($data as $da) {
                        if (! empty($da) && count($da) > 0) {
                            foreach ($da as $response) {
                                if (array_key_exists('orderPayment', $response)) {
                                    foreach ($response['orderPayment'] as $order_response) {
                                        $insert = [
                                            'website_id' => $web->id,
                                            'entity_id' => $order_response['entity_id'],
                                            'parent_id' => $order_response['parent_id'],
                                            'base_shipping_captured' => $order_response['base_shipping_captured'],
                                            'shipping_captured' => $order_response['shipping_captured'],
                                            'amount_refunded' => $order_response['amount_refunded'],
                                            'base_amount_paid' => $order_response['base_amount_paid'],
                                            'amount_canceled' => $order_response['amount_canceled'],
                                            'base_amount_authorized' => $order_response['base_amount_authorized'],
                                            'base_amount_paid_online' => $order_response['base_amount_paid_online'],
                                            'base_amount_refunded_online' => $order_response['base_amount_refunded_online'],
                                            'base_shipping_amount' => $order_response['base_shipping_amount'],
                                            'shipping_amount' => $order_response['shipping_amount'],
                                            'amount_paid' => $order_response['amount_paid'],
                                            'amount_authorized' => $order_response['amount_authorized'],
                                            'base_amount_ordered' => $order_response['base_amount_ordered'],
                                            'base_shipping_refunded' => $order_response['base_shipping_refunded'],
                                            'shipping_refunded' => $order_response['shipping_refunded'],
                                            'base_amount_refunded' => $order_response['base_amount_refunded'],
                                            'amount_ordered' => $order_response['amount_ordered'],
                                            'base_amount_canceled' => $order_response['base_amount_canceled'],
                                            'quote_payment_id' => $order_response['quote_payment_id'],
                                            'cc_exp_month' => $order_response['cc_exp_month'],
                                            'cc_ss_start_year' => $order_response['cc_ss_start_year'],
                                            'cc_secure_verify' => $order_response['cc_secure_verify'],
                                            'cc_approval' => $order_response['cc_approval'],
                                            'cc_last_4' => $order_response['cc_last_4'],
                                            'cc_type' => $order_response['cc_type'],
                                            'cc_exp_year' => $order_response['cc_exp_year'],
                                            'cc_status' => $order_response['cc_status'],
                                        ];
                                        $response_exist = PaymentResponse::where('website_id', $web->id)->where('entity_id', $order_response['entity_id'])->first();
                                        if (! empty($response_exist)) {
                                            $response_exist->update($insert);
                                        } else {
                                            PaymentResponse::create($insert);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $report->update(['end_time' => Carbon::now()]);

            return 'successfully';
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    /**
     * Get API Data
     *
     * @param  \App\StoreWebsite  $url,$token
     *                                         return Response Json
     * @param  mixed  $token
     */
    public function getDataApi(StoreWebsite $url, $token)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $authorization = 'Authorization: Bearer '.$token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]); // Inject the token into the header
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($server_output), $httpcode, GetPaymentResponses::class, 'getDataApi');

        return $server_output;
    }
}
