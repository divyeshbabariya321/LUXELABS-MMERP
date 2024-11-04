<?php

namespace App\Jobs;

use App\WatsonAccount;
use App\ChatbotErrorLog;
use App\ChatbotQuestion;
use Illuminate\Bus\Queueable;
use App\ChatbotDialogErrorLog;
use App\ChatbotQuestionErrorLog;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Library\Watson\Language\Workspaces\V1\DialogService;
use App\Library\Watson\Language\Workspaces\V1\IntentService;
use App\Library\Watson\Language\Workspaces\V1\EntitiesService;
use Exception;

class ManageWatson implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param protected      $service
     * @param protected      $question
     * @param protectedarray $storeParams
     * @param protected      $method
     * @param protected      $type
     * @param protected      $old_example
     * @param null|protected $oldValue
     *
     * @return void
     */
    public function __construct(protected $service, protected $question, protected array $storeParams, protected $method, protected $type = 'value', protected $old_example = false, protected $oldValue = null)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $all_watson_accounts = WatsonAccount::get();
            if ($this->type) {
                $value = $this->question->{$this->type};
            }
            $serviceClass = 'IntentService';

            if ($this->service === 'dialog') {
                $serviceClass = 'DialogService';
            } elseif ($this->service === 'entity') {
                $serviceClass = 'EntitiesService';
            }

            foreach ($all_watson_accounts as $account) {
                if ($this->service === 'dialog') {
                    $watson = new DialogService(
                        'apiKey',
                        $account->api_key
                    );
                } elseif ($this->service === 'entity') {
                    $watson = new EntitiesService(
                        'apiKey',
                        $account->api_key
                    );
                    $value = $this->oldValue;
                } else {
                    $watson = new IntentService(
                        'apiKey',
                        $account->api_key
                    );
                    $value = $this->oldValue;
                }
                $watson->set_url($account->url);
                if ($this->method === 'create') {
                    $result = $watson->create($account->work_space_id, $this->storeParams);
                } elseif ($this->method === 'update') {
                    $result = $watson->update($account->work_space_id, $value, $this->storeParams);
                } elseif ($this->method === 'delete') {
                    $result = $watson->delete($account->work_space_id, $value);
                } elseif ($this->method === 'update_example') {
                    $result = $watson->updateExample($account->work_space_id, $value, $this->old_example, $this->storeParams);
                }

                $status = $result->getStatusCode();
                if ($status == 201 || $status == 200) {
                    $success = 1;
                } else {
                    $success = 0;
                }
                if ($this->service === 'dialog') {
                    $errorlog                    = new ChatbotDialogErrorLog;
                    $errorlog->chatbot_dialog_id = $this->question->id;
                    $errorlog->reply_id          = $this->storeParams['reply_id'];
                    $errorlog->store_website_id  = $account->store_website_id;
                    $errorlog->request           = json_encode($this->storeParams);
                    $errorlog->status            = $success;
                    $errorlog->response          = $result->getContent();
                    $errorlog->save();

                    ChatbotQuestion::where('id', $this->question->id)->update(['watson_status' => $success]);
                } else {
                    ChatbotQuestion::where('id', $this->question->id)->update(['watson_status' => $success]);

                    $errorlog                      = new ChatbotErrorLog;
                    $errorlog->chatbot_question_id = $this->question->id;
                    $errorlog->store_website_id    = $account->store_website_id;
                    $errorlog->status              = $success;
                    $errorlog->response            = $result->getContent();
                    $errorlog->save();

                    $question_error                      = new ChatbotQuestionErrorLog();
                    $question_error->chatbot_question_id = $this->question->id;
                    $question_error->type                = $this->service;
                    $question_error->request             = json_encode($this->storeParams);
                    $question_error->response            = $errorlog->response;
                    $question_error->response_type       = $success == 0 ? 'error' : 'success';
                    $question_error->save();
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['watson_push', $this->question->id];
    }

    public function fail($exception = null)
    {
        /* Remove data when job fail while creating..... */
        if ($this->method === 'create' && is_object($this->question)) {
            $this->question->delete();
        }
    }
}
