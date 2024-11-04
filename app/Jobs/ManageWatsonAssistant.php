<?php

namespace App\Jobs;
use App\ChatbotMessageLogResponse;

use App\Library\Watson\Language\Assistant\V2\AssistantService;
use App\Library\Watson\Model;
use App\WatsonAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class ManageWatsonAssistant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param  protected  $customer
     * @param  protected  $inputText
     * @param  protected  $contextReset
     * @param  protected  $message_application_id
     * @param  null|protected  $messageModel
     * @param  null|protected  $userType
     * @param  null|protected  $chat_message_log_id
     */

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (isset($this->chat_message_log_id)) {
                ChatbotMessageLogResponse::StoreLogResponse([
                    'chatbot_message_log_id' => $this->chat_message_log_id,
                    'request' => '',
                    'response' => 'Watson asistantant function job dispatched started',
                    'status' => 'success',
                ]);
            }

            $store_website_id = ($this->customer->store_website_id > 0) ? $this->customer->store_website_id : 1;

            $account = WatsonAccount::where('store_website_id', $store_website_id)->first();
            if ($account) {
                $asistant = new AssistantService(
                    'apiKey',
                    $account->api_key
                );
                $asistant->set_url($account->url);

                if (isset($this->chat_message_log_id)) {
                    ChatbotMessageLogResponse::StoreLogResponse([
                        'chatbot_message_log_id' => $this->chat_message_log_id,
                        'request' => '',
                        'response' => 'Watson asistantant function send message from job started with account'.$account->api_key,
                        'status' => 'success',
                    ]);
                }

                Model::sendMessageFromJob($this->customer, $account, $asistant, $this->inputText, $this->contextReset, $this->message_application_id, $this->messageModel, $this->userType, $this->chat_message_log_id);
            } else {
                if (isset($this->chat_message_log_id)) {
                    ChatbotMessageLogResponse::StoreLogResponse([
                        'chatbot_message_log_id' => $this->chat_message_log_id,
                        'request' => '',
                        'response' => 'Watson asistantant function job account not found',
                        'status' => 'success',
                    ]);
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['watson_push', $this->message_application_id];
    }

    public function fail($exception = null)
    {
        $data = [
            'chatbot_message_log_id' => $this->chat_message_log_id,
            'request' => '',
            'response' => 'Watson asistant queue failed.',
            'status' => 'failed',
        ];
        $chat_message_log = ChatbotMessageLogResponse::StoreLogResponse($data);
        /* Remove data when job fail while creating..... */
        if ($this->method === 'create' && is_object($this->question)) {
            $this->question->delete();
        }
    }
}
