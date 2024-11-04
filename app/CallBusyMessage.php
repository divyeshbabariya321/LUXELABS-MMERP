<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CallBusyMessage extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="lead_id",type="integer")
     * @SWG\Property(property="twilio_call_sid",type="string")
     * @SWG\Property(property="caller_sid",type="string")
     * @SWG\Property(property="message",type="string")
     * @SWG\Property(property="recording_url",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="call_busy_messages",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    protected $fillable = ['lead_id', 'twilio_call_sid', 'caller_sid', 'message', 'recording_url', 'status', 'call_busy_message_statuses_id', 'audio_text'];

    /**
     * Function to insert large amount of data
     *
     * @param  type  $data
     * @return $result
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            if ($model->twilio_call_sid) {
                $formatted_phone = str_replace('+', '', $model->twilio_call_sid);
                $customer = Customer::with('storeWebsite', 'orders')->where('phone', $formatted_phone)->first();
                $model->customer_id = $customer->id ?? null;
                $model->save();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'created_atcreated_at' => 'datetime',
        ];
    }

    public static function bulkInsert($data)
    {
        CallBusyMessage::insert($data);
    }

    /**
     * Function to check Twilio Sid
     *
     * @param  string  $sId  twilio sid
     */
    public static function checkSidAlreadyExist(string $sId)
    {
        return CallBusyMessage::where('caller_sid', '=', $sId)->first();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(CallBusyMessageStatus::class, 'call_busy_message_statuses_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    // public function convertSpeechToText($recording_url, $store_website_id = 0, $to = null, $from = null)
    // {
    //     $recording_url = trim($recording_url);
    //     $ch1           = curl_init();
    //     curl_setopt($ch1, CURLOPT_URL, $recording_url);
    //     curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
    //     $http_respond = curl_exec($ch1);
    //     $http_respond = trim(strip_tags($http_respond));
    //     $http_code    = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
    //     curl_close($ch1);
    //     if (($http_code == '200') || ($http_code == '302')) {
    //         // If store id not found
    //         if ($store_website_id == 0) {
    //             // Check To number is exist in DB
    //             $twilioActive = TwilioActiveNumber::where('phone_number', $to)->first();

    //             if (! empty($twilioActive)) {
    //                 $store_website_id = $twilioActive->assigned_stores->store_website_id ?? 0;
    //             } else {
    //                 // Check From number is exist in DB
    //                 $customerInfo = Customer::where('phone', str_replace('+', '', $from))->first();

    //                 if (! empty($customerInfo)) {
    //                     $store_website_id = $customerInfo->store_website_id ?? 0;
    //                 }
    //             }
    //         }

    //         // Get watson account associated with store websites
    //         $watsonAccount = WatsonAccount::where('store_website_id', $store_website_id)->first();

    //         // Check if watson account is linked with store website
    //         if (empty($watsonAccount)) {
    //             return '';
    //         }

    //         // Watson account is linked but speech to text URL not available
    //         if (empty($watsonAccount->speech_to_text_url) || empty($watsonAccount->speech_to_text_api_key)) {
    //             return '';
    //         }

    //         $apiKey = $watsonAccount->speech_to_text_api_key;
    //         $url    = $watsonAccount->speech_to_text_url;

    //         $ch   = curl_init();
    //         $file = file_get_contents($recording_url);
    //         curl_setopt($ch, CURLOPT_URL, $url . '/v1/recognize?model=en-US_NarrowbandModel');
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //         curl_setopt($ch, CURLOPT_POST, 1);

    //         curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
    //         curl_setopt($ch, CURLOPT_POST, 1);
    //         curl_setopt($ch, CURLOPT_USERPWD, 'apikey' . ':' . $apiKey);

    //         $headers   = [];
    //         $headers[] = 'Content-Type: application/octet-stream';
    //         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //         $result = curl_exec($ch);
    //         if (curl_errno($ch)) {
    //             echo 'Error:' . curl_error($ch);
    //         }
    //         curl_close($ch);
    //         $result = json_decode($result);

    //         // If result found
    //         if (! empty($result->results) && count($result->results) > 0) {
    //             return $result->results[0]->alternatives[0]->transcript;
    //         } else {
    //             return '';
    //         }
    //     } else {
    //         return '';
    //     }
    // }

    public function convertSpeechToText($recording_url, $store_website_id = 0, $to = null, $from = null)
    {
        $recording_url = trim($recording_url);

        $http_respond = $this->fetchRecording($recording_url);
        if (! $this->isValidHttpResponse($http_respond)) {
            return '';
        }

        $store_website_id = $this->getStoreWebsiteId($store_website_id, $to, $from);
        $watsonAccount = $this->getWatsonAccount($store_website_id);

        if (! $watsonAccount || ! $this->hasValidWatsonCredentials($watsonAccount)) {
            return '';
        }

        $transcript = $this->getTranscriptFromWatson($recording_url, $watsonAccount->speech_to_text_url, $watsonAccount->speech_to_text_api_key);

        return $transcript ?: '';
    }

    private function fetchRecording($recording_url)
    {
        $ch1 = curl_init();
        curl_setopt($ch1, CURLOPT_URL, $recording_url);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
        $http_respond = curl_exec($ch1);
        $http_respond = trim(strip_tags($http_respond));
        curl_close($ch1);

        return trim(strip_tags($http_respond));
    }

    private function isValidHttpResponse($response)
    {
        $http_code = curl_getinfo($response, CURLINFO_HTTP_CODE);

        return ($http_code == '200') || ($http_code == '302');
    }

    private function getStoreWebsiteId($store_website_id, $to, $from)
    {
        if ($store_website_id == 0) {
            $twilioActive = TwilioActiveNumber::where('phone_number', $to)->first();
            if (! empty($twilioActive)) {
                return $twilioActive->assigned_stores->store_website_id ?? 0;
            }

            $customerInfo = Customer::where('phone', str_replace('+', '', $from))->first();
            if (! empty($customerInfo)) {
                return $customerInfo->store_website_id ?? 0;
            }
        }

        return $store_website_id;
    }

    private function getWatsonAccount($store_website_id)
    {
        return WatsonAccount::where('store_website_id', $store_website_id)->first();
    }

    private function hasValidWatsonCredentials($watsonAccount)
    {
        return ! empty($watsonAccount->speech_to_text_url) && ! empty($watsonAccount->speech_to_text_api_key);
    }

    private function getTranscriptFromWatson($recording_url, $url, $apiKey)
    {
        $ch = curl_init();
        $file = file_get_contents($recording_url);
        curl_setopt($ch, CURLOPT_URL, $url.'/v1/recognize?model=en-US_NarrowbandModel');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, 'apikey'.':'.$apiKey);

        $headers = [];
        $headers[] = 'Content-Type: application/octet-stream';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:'.curl_error($ch);
        }
        curl_close($ch);

        $result = json_decode($result);

        return ! empty($result->results) ? $result->results[0]->alternatives[0]->transcript : '';
    }
}
