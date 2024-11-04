<?php

namespace App\Jobs;
use App\StoreWebsiteTwilioNumber;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Customer;
use App\ChatMessage;
use Twilio\Rest\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class TwilioSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $account_sid;

    public $auth_token;

    public $twilio_number;

    public $receiverNumber;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param mixed       $receiverNumber
     * @param public      $message
     * @param public      $store_website_id
     * @param null|public $orderId
     *
     * @return void
     */
    public function __construct($receiverNumber, public $message, public $store_website_id, public $orderId = null)
    {
        $this->receiverNumber = '+' . $receiverNumber;

        $twilio_cred = StoreWebsiteTwilioNumber::select('twilio_active_numbers.account_sid as a_sid', 'twilio_active_numbers.phone_number as phone_number', 'twilio_credentials.auth_token as auth_token')
            ->join('twilio_active_numbers', 'twilio_active_numbers.id', '=', 'store_website_twilio_numbers.twilio_active_number_id')
            ->join('twilio_credentials', 'twilio_credentials.id', '=', 'twilio_active_numbers.twilio_credential_id')
            ->where('store_website_twilio_numbers.store_website_id', $this->store_website_id)
            ->first();

        if (isset($twilio_cred)) {
            $this->account_sid   = $twilio_cred->a_sid;
            $this->auth_token    = $twilio_cred->auth_token;
            $this->twilio_number = $twilio_cred->phone_number;
        } else {
            $this->account_sid   = 'AC23d37fbaf2f8a851f850aa526464ee7d';
            $this->auth_token    = '51e2bf471c33a48332ea365ae47a6517';
            $this->twilio_number = '+18318880662';
        }

        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        //
        try {
            $client = new Client($this->account_sid, $this->auth_token);
            $client->messages->create($this->receiverNumber, [
                'from' => $this->twilio_number,
                'body' => $this->message,
            ]);
            $phone  = str_replace('+', '', $this->receiverNumber);
            $custId = Customer::where('phone', 'like', '%' . $phone . '%')->pluck('id')->first();
            $chat   = [
                'message_application_id' => 3,
                'message'                => $this->message,
                'number'                 => $this->receiverNumber,
                'send_by'                => $this->twilio_number,
                'user_id'                => Auth::id(),
                'approved'               => 1,
                'is_delivered'           => 1,
                'customer_id'            => $custId,
                'order_id'               => $this->orderId,
            ];
            ChatMessage::create($chat);
        } catch (Exception $e) {
            Log::info('Sending SMS issue #2215 ->' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['TwilioSmsJob', $this->store_website_id];
    }
}
