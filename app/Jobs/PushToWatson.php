<?php

namespace App\Jobs;

use App\WatsonAccount;
use App\ChatbotErrorLog;
use App\ChatbotQuestion;
use App\ChatbotQuestionReply;
use Illuminate\Bus\Queueable;
use App\ChatbotQuestionExample;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Library\Watson\Language\Workspaces\V1\DialogService;
use App\Library\Watson\Language\Workspaces\V1\IntentService;
use App\Library\Watson\Language\Workspaces\V1\EntitiesService;
use Exception;

class PushToWatson implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 5;

    public $backoff = 5;

    public function __construct(protected $account_id)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $account = WatsonAccount::find($this->account_id);
            $intents = ChatbotQuestion::all();
            foreach ($intents as $question) {
                if (config('settings.push_watson') == false) {
                    return;
                }
                $success = 0;

                $storeParams                                 = [];
                $storeParams[$question->keyword_or_question] = $question->value;
                $values                                      = $question->chatbotQuestionExamples()->get();

                if ($question->keyword_or_question == 'entity') {
                    foreach ($values as $value) {
                        $typeValue = ChatbotQuestionExample::where('id', $value['id'])->get()->pluck('question');
                        if ($value['types'] == 'synonyms') {
                            $storeParams['values'][] = ['value' => $value['question'], 'synonyms' => $typeValue];
                        } else {
                            $storeParams['values'][] = ['value' => $value['question'], 'type' => 'patterns', 'patterns' => $typeValue];
                        }
                    }
                }
                if ($question->keyword_or_question == 'intent') {
                    $storeParams['examples'] = [];
                    foreach ($values as $k => $value) {
                        $storeParams['examples'][$k]['text'] = $value->question;
                        $mentions                            = $value->annotations;
                        if (! $mentions->isEmpty()) {
                            $sendMentions = [];
                            foreach ($mentions as $key => $mRaw) {
                                if ($mRaw->chatbotKeyword) {
                                    $sendMentions[] = [
                                        'entity'   => $mRaw->chatbotKeyword->keyword,
                                        'location' => [$mRaw->start_char_range, $mRaw->end_char_range],
                                    ];
                                }
                            }
                            if (! empty($sendMentions)) {
                                $storeParams['examples'][$k]['mentions'] = $sendMentions;
                            }
                        }
                    }
                }

                $serviceClass = 'IntentService';

                if ($question->keyword_or_question === 'dialog') {
                    $serviceClass = 'DialogService';
                } elseif ($question->keyword_or_question === 'entity') {
                    $serviceClass = 'EntitiesService';
                }

                if ($question->keyword_or_question === 'dialog') {
                    $watson = new DialogService(
                        'apiKey',
                        $account->api_key
                    );
                } elseif ($question->keyword_or_question === 'entity') {
                    $watson = new EntitiesService(
                        'apiKey',
                        $account->api_key
                    );
                } else {
                    $watson = new IntentService(
                        'apiKey',
                        $account->api_key
                    );
                }
                $watson->set_url($account->url);
                $result = $watson->create($account->work_space_id, $storeParams);
                $status = $result->getStatusCode();
                if ($status == 400) {
                    $result = $watson->update($account->work_space_id, $question->value, $storeParams);
                    $st     = $result->getStatusCode();
                    if ($st == 201 || $st == 200) {
                        $success = 1;
                    }
                } elseif ($status == 201 || $status == 200) {
                    $success = 1;
                } else {
                    $success = 0;
                }
                ChatbotErrorLog::where('store_website_id', $account->store_website_id)->where('chatbot_question_id', $question->id)->delete();
                $errorlog                      = new ChatbotErrorLog;
                $errorlog->chatbot_question_id = $question->id;
                $errorlog->store_website_id    = $account->store_website_id;
                $errorlog->status              = $success;
                $errorlog->response            = $result->getContent();
                $errorlog->save();
                if ($success) {
                    $reply = ChatbotQuestionReply::where('store_website_id', $account->store_website_id)->where('chatbot_question_id', $question->id)->first();
                    if (! $reply) {
                        $anyReply = ChatbotQuestionReply::where('chatbot_question_id', $question->id)->first();
                        if ($anyReply) {
                            $reply                      = new ChatbotQuestionReply;
                            $reply->store_website_id    = $account->store_website_id;
                            $reply->chatbot_question_id = $question->id;
                            $reply->suggested_reply     = $anyReply->suggested_reply;
                            $reply->save();
                        }
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['watson_push', $this->account_id];
    }
}
