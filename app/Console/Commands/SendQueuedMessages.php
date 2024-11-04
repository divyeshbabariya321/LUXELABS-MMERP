<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\StoreWebsiteTwilioNumber;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SendQueuedMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send_message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $chatMessage = ChatMessage::where('is_queue', 1)
            ->join('customers as c', 'c.id', 'chat_messages.customer_id')
            ->where('chat_messages.message_application_id', 3)
            ->where(function ($q) {
                $q->whereNull('chat_messages.scheduled_at')
                    ->orWhere('chat_messages.scheduled_at', 'like', Carbon::now()->format('Y-m-d').'%');
            })
            ->select('chat_messages.*', 'c.store_website_id', 'c.phone')
            ->get();

        if (! $chatMessage->isEmpty()) {
            $this->sendMessages($chatMessage);
        }

        $chatMessage1 = ChatMessage::where('is_queue', 1)
            ->join('users as c', 'c.id', 'chat_messages.user_id')
            ->where('chat_messages.message_application_id', 3)
            ->where(function ($q) {
                $q->whereNull('chat_messages.scheduled_at')
                    ->orWhere('chat_messages.scheduled_at', 'like', Carbon::now()->format('Y-m-d').'%');
            })
            ->select('chat_messages.*', 'c.phone')
            ->get();
        if (! $chatMessage1->isEmpty()) {
            $this->sendMessages($chatMessage1);
        }
    }

    public function sendMessages($chatMessage)
    {
        foreach ($chatMessage as $value) {
            $twilio_cred = null;
            if (isset($value['store_website_id'])) {
                $twilio_cred = StoreWebsiteTwilioNumber::select('twilio_active_numbers.account_sid as a_sid', 'twilio_active_numbers.phone_number as phone_number', 'twilio_credentials.auth_token as auth_token')
                    ->join('twilio_active_numbers', 'twilio_active_numbers.id', '=', 'store_website_twilio_numbers.twilio_active_number_id')
                    ->join('twilio_credentials', 'twilio_credentials.id', '=', 'twilio_active_numbers.twilio_credential_id')
                    ->where('store_website_twilio_numbers.store_website_id', $value->store_website_id)
                    ->first();
            }

            if (isset($twilio_cred) && $twilio_cred != null) {
                $account_sid = $twilio_cred->a_sid;
                $auth_token = $twilio_cred->auth_token;
                $twilio_number = $twilio_cred->phone_number;
            } else {
                $account_sid = 'AC23d37fbaf2f8a851f850aa526464ee7d';
                $auth_token = '51e2bf471c33a48332ea365ae47a6517';
                $twilio_number = '+18318880662';
            }
            try {
                $receiverNumber = '+'.$value['phone'];

                $message = $value['message'];
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($receiverNumber, [
                    'from' => $twilio_number,
                    'body' => $message,
                ]);
                ChatMessage::where('id', $value['id'])->update([
                    'send_by' => $twilio_number,
                    'user_id' => Auth::id(),
                    'approved' => 1,
                    'is_delivered' => 1,
                    'is_queue' => 0,
                ]);
            } catch (Exception $e) {
                Log::info('Sending SMS issue #2215 ->'.$e->getMessage());
            }
        }
    }
}
