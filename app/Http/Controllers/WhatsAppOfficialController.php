<?php

namespace App\Http\Controllers;

use App\LogRequest;
use App\Marketing\WhatsappBusinessAccounts;

class WhatsAppOfficialController extends Controller
{
    private $account;

    private $BASE_API_URL = 'https://graph.facebook.com/v16.0/';

    private $ignorePhoneNumber;

    public function __construct($accountId)
    {
        $this->account           = WhatsappBusinessAccounts::find($accountId);
        $this->ignorePhoneNumber = false;
    }

    public function updateBusinessProfile($parameters)
    {
        $response = $this->callApi('POST', 'whatsapp_business_profile', $parameters);
        if ($response['data']['success']) {
            unset($parameters['_token']);
            unset($parameters['edit_id']);
            foreach ($parameters as $key => $parameter) {
                $this->account->{$key} = $parameter;
            }
            $this->account->save();
        }
        $this->uploadMedia([
            'file' => $parameters['profile_picture_url']->getPathname(),
            'type' => $parameters['profile_picture_url']->getClientMimeType(),
        ]);
        if ($response['data']['success']) {
            $this->account->profile_picture_url = $response['data']['url'];
            $this->account->save();
        }
    }

    public function uploadMedia($parameters): array
    {
        $this->ignorePhoneNumber = true;
        $response                = $this->callApi('POST', 'media', $parameters);
        if ($response['data']['id']) {
            $urlResponse             = $this->callApi('GET', $response['data']['id']);
            $response['data']['url'] = $urlResponse['data']['url'];
        }

        return $response;
    }

    public function sendMessage($params): array
    {
        $buildMessagesParams = $this->buildMessageParams($params);

        return $this->callApi('POST', 'messages', $buildMessagesParams);
    }

    /**
     * @param mixed $params
     *
     * @return string[]
     */
    public function buildMessageParams($params): array
    {
        $finalParams = ['messaging_product' => 'whatsapp', 'recipient_type' => 'individual', 'status' => 'read'];
        if (isset($params['isReply'])) {
            $finalParams['context'] = ['message_id' => $params['message_id']];
        }
        if ($params['type'] == 'audio' || $params['type'] == 'document' || $params['type'] == 'image') {
            $mediaResponse = $this->uploadMedia([
                'file' => $params['file']->getPathname(),
                'type' => $params['file']->getClientMimeType(),
            ]);
            $finalParams = $finalParams + [
                $params['type'] => [
                    'id'       => $mediaResponse['data']['id'],
                    'filename' => $params['file']->getClientOriginalName(),
                ],
                'to'   => $params['number'],
                'type' => $params['type'],
            ];
        } else {
            $finalParams = $finalParams + [
                'text' => [
                    'body'        => $params['body'],
                    'preview_url' => $params['preview_url'],
                ],
                'to'          => $params['number'],
                'type'        => 'text',
                'preview_url' => true,
            ];
        }

        return $finalParams;
    }

    /**
     * Common function to fetch data from API using CURL.
     *
     * @param mixed $method
     * @param mixed $url
     */
    public function callApi($method, $url, array $params = []): array
    {
        $finalUrl  = $this->BASE_API_URL;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        if (! $this->ignorePhoneNumber) {
            $finalUrl .= $this->account->business_phone_number_id . '/';
        }
        $finalUrl .= $url;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $finalUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->account->business_access_token,
            ],
        ]);

        if ($method == 'POST' || $method == 'PATCH' || $method == 'PUT') {
            if (! isset($params['messaging_product'])) {
                $params['messaging_product'] = 'whatsapp';
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        LogRequest::log($startTime, $finalUrl, $method, json_encode($params), $response, $httpcode, WhatsAppOfficialController::class, 'callApi');
        if ($err) {
            $message = 'Account :- ' . $this->account->id . ', ';

            return ['status' => false, 'message' => $message . 'cURL Error #:' . $err];
        } else {
            $response = json_decode($response, true);
            if (is_array($response)) {
                return ['status' => true, 'message' => 'Data found', 'data' => $response];
            } else {
                $message = 'Account :- ' . $this->account->id . ', ';

                return ['status' => false, 'message' => $message . 'cURL Error #:' . $response];
            }
        }
    }
}
