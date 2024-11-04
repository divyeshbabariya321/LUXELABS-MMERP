<?php

namespace App\Http\Controllers;

use App\CallRecording;
use App\Customer;
use App\LogRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExotelController extends FindByNumberController
{
    public function call($curl_setopt)
    {
        $post_data = [
            'From' => '02248931265',
            'To' => '02225921525',
            'CallerId' => '02248931265',
            'CallType' => 'promo', //Can be "trans" for transactional and "promo" for promotional content
        ];

        $exotel_sid = 'sololuxury'; // Your Exotel SID - Get it from here: https://my.exotel.in/settings/site#api-settings
        $exotel_token = '815a3a4dbf47e348d5f45c19c4067de14c120046'; // Your exotel token - Get it from here: https://my.exotel.in/settings/site#api-settings
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = 'https://'.$exotel_sid.':'.$exotel_token.'@twilix.exotel.in/v1/Accounts/'.$exotel_sid.'/Calls/connect';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $http_result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        LogRequest::log($startTime, $url, 'POST', json_encode($post_data), json_decode($http_result), $http_code, ExotelController::class, 'Exotelcall');
        echo 'Response = '.print_r($http_result);
    }

    public function checkNumber(Request $request): Response
    {
        try {
            $number = $request->get('From');

            [$context] = $this->findCustomerOrLeadOrOrderByNumber(str_replace('+', '', $number));

            if (! $context) {
                $customer = new Customer;

                $customer->name = 'Customer from Call';
                $customer->phone = $number;
                $customer->rating = 1;

                $customer->save();
            }

            return response('success');
        } catch (Exception $ex) {
            return response('error', 302);
        }
    }

    public function recordingCallback(Request $request): Response
    {
        $url = $request->get('RecordingUrl');
        $sid = $request->get('CallSid');
        $params = [
            'recording_url' => $url,
            'twilio_call_sid' => $sid,
            'callsid' => $sid,
        ];
        $context = $request->get('context');
        $internalId = $request->get('internalId');

        if ($context && $internalId) {
            if ($context == 'leads') {
                $params['lead_id'] = $internalId;
            } elseif ($context == 'orders') {
                $params['order_id'] = $internalId;
            } elseif ($context == 'customers') {
                $params['customer_id'] = $internalId;
            }
        }

        $customer_mobile = $request->get('From');

        if ($customer_mobile != '') {
            $params['customer_number'] = $customer_mobile;
        }

        CallRecording::create($params);

        return response('success');
    }
}
