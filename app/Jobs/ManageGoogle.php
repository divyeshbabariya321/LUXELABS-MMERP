<?php

namespace App\Jobs;

use App\ChatbotQuestion;
use Illuminate\Bus\Queueable;
use App\Models\GoogleResponseId;
use App\Models\GoogleDialogAccount;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Library\Google\DialogFlow\DialogFlowService;
use Exception;

class ManageGoogle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param protected      $questionId
     * @param protectedarray $storeParams
     *
     * @return void
     */
    public function __construct(protected $questionId, protected array $storeParams)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $google_accounts = GoogleDialogAccount::all();
        $chatBotQuestion = ChatbotQuestion::where('id', $this->questionId)->first();
        if ($chatBotQuestion) {
            $questionArr = [];
            $replyArr    = [];
            foreach ($chatBotQuestion->chatbotQuestionExamples as $question) {
                $questionArr[] = $question->question;
            }
            $replyArr = explode(',', $chatBotQuestion->suggested_reply);
            foreach ($chatBotQuestion->chatbotQuestionReplies as $reply) {
                $replyArr[] = $reply->suggested_reply;
            }
            foreach ($google_accounts as $google_account) {
                $dialogService = new DialogFlowService($google_account);
                try {
                    $response = $dialogService->createIntent([
                        'questions' => $questionArr,
                        'reply'     => $replyArr,
                        'name'      => $chatBotQuestion['value'],
                        'parent'    => $chatBotQuestion['parent']]);
                    if ($response) {
                        $name                                     = explode('/', $response);
                        $store_response                           = new GoogleResponseId();
                        $store_response->google_response_id       = $name[count($name) - 1];
                        $store_response->google_dialog_account_id = $google_account->id;
                        $store_response->chatbot_question_id      = $chatBotQuestion->id;
                        $store_response->save();
                    }
                } catch (Exception $e) {
                    $chatBotQuestion->google_status = $e->getMessage();
                    $chatBotQuestion->save();
                }
            }
        }
    }
}
