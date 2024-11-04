<?php

namespace App\Observers;

use App\ChatbotQuestion;
use App\ChatbotQuestionReply;
use App\ChatMessage;
use App\Customer;
use App\Http\Controllers\WhatsAppController;
use App\Library\Google\DialogFlow\DialogFlowService;
use App\Models\GoogleDialogAccount;
use App\Models\TmpReplay;
use App\ReplyCategory;
use App\Supplier;
use App\Vendor;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ChatMessageObserver
{
    /**
     * Handle the ChatMessage "created" event.
     */
    public function created(ChatMessage $chatMessage): void
    {
        try {
            $session = Cache::get('chatbot-session');
            if (! $session) {
                $session = uniqid();
                Cache::set('chatbot-session', $session);
            }
            $object = '';
            $objectType = '';
            if ($chatMessage->vendor_id > 0) {
                $objectType = 'vendor';
                $object = Vendor::find($chatMessage->vendor_id);
            } elseif ($chatMessage->customer_id > 0) {
                $objectType = 'customer';
                $object = Customer::find($chatMessage->customer_id);
            } elseif ($chatMessage->supplier_id) {
                $objectType = 'supplier';
                $object = Supplier::find($chatMessage->supplier_id);
            }
            if ($chatMessage->vendor_id || $chatMessage->customer_id || $chatMessage->supplier_id) {
                if ($chatMessage->customer_id > 0) {
                    $googleAccount = GoogleDialogAccount::where('site_id', $object->store_website_id)->first();
                } else {
                    $googleAccount = GoogleDialogAccount::where('default_selected', 1)->first();
                }
                $isOut = ($chatMessage->number != $object->phone) ? true : false;
                if ($object->is_auto_simulator == 1 && ! $isOut) {
                    $chatQuestions = ChatbotQuestion::leftJoin('chatbot_question_examples as cqe', 'cqe.chatbot_question_id', 'chatbot_questions.id')
                        ->leftJoin('chatbot_categories as cc', 'cc.id', 'chatbot_questions.category_id')
                        ->select('chatbot_questions.*', DB::raw('group_concat(cqe.question) as `questions`'), 'cc.name as category_name')
                        ->where('chatbot_questions.google_account_id', 1)
                        ->where('chatbot_questions.keyword_or_question', 'intent')
                        ->where('chatbot_questions.value', 'like', '%'.$chatMessage->message.'%')->orWhere('cqe.question', 'like', '%'.$chatMessage->message.'%')
                        ->groupBy('chatbot_questions.id')
                        ->orderByDesc('chatbot_questions.id')
                        ->first();
                    $requestData = [
                        'chat_id' => $chatMessage->id,
                        'status' => 2,
                        'add_autocomplete' => false,
                    ];
                    if ($chatMessage->vendor_id > 0) {
                        $requestData['vendor_id'] = $chatMessage->vendor_id;
                    } elseif ($chatMessage->customer_id > 0) {
                        $requestData['customer_id'] = $chatMessage->customer_id;
                    } elseif ($chatMessage->supplier_id) {
                        $requestData['supplier_id'] = $chatMessage->supplier_id;
                    }
                    $dialogFlowService = new DialogFlowService($googleAccount);

                    $getFromGoogle = true;
                    if ($chatQuestions) {
                        if ($chatQuestions->auto_approve == 1) {
                            $requestData['message'] = $dialogFlowService->purifyResponse($chatQuestions->suggested_reply, $objectType == 'customer' ? $object : null);
                            $request = Request::create('/', 'POST', $requestData);
                            app(WhatsAppController::class)->sendMessage($request, $objectType);
                            $getFromGoogle = false;
                        }
                    } else {
                        $replay = ReplyCategory::join('replies', 'reply_categories.id', 'replies.category_id')
                            ->select(['replies.*', 'reply_categories.intent_id', 'reply_categories.name as category_name', 'reply_categories.parent_id', 'reply_categories.id as reply_cat_id'])
                            ->Where('reply_categories.name', 'LIKE', '%'.$chatMessage->message.'%')
                            ->first();

                        if ($replay) {
                            $requestData['message'] = $dialogFlowService->purifyResponse($replay->replay, $objectType == 'customer' ? $object : null);
                            $request = Request::create('/', 'POST', $requestData);
                            app(WhatsAppController::class)->sendMessage($request, $objectType);
                            $getFromGoogle = false;
                        }
                    }

                    if ($getFromGoogle) {
                        $response = $dialogFlowService->detectIntent($session, $chatMessage->message);
                        $intentName = $response->getIntent()->getName();
                        $intentName = explode('/', $intentName);
                        $intentName = $intentName[count($intentName) - 1];

                        $question = ChatbotQuestion::where('google_response_id', $intentName)->first();
                        if (! $question) {
                            $question = ChatbotQuestion::where('value', $response->getIntent()->getDisplayName())->first();
                            if (! $question) {
                                $question = ChatbotQuestion::create([
                                    'keyword_or_question' => 'intent',
                                    'is_active' => true,
                                    'google_account_id' => $googleAccount->id,
                                    'google_status' => '1',
                                    'google_response_id' => $intentName,
                                    'value' => $response->getIntent()->getDisplayName(),
                                    'suggested_reply' => $response->getFulfillmentText(),
                                ]);
                            }
                        }

                        $questionsR = ChatbotQuestionReply::where('suggested_reply', 'like', '%'.$response->getFulfillmentText().'%')->first();
                        if (! $questionsR) {
                            $chatRply = new ChatbotQuestionReply;
                            $chatRply->suggested_reply = $response->getFulfillmentText();
                            $chatRply->store_website_id = $googleAccount->site_id;
                            $chatRply->chatbot_question_id = $question->id;
                            $chatRply->save();
                        }
                        $store_replay = new TmpReplay;
                        $store_replay->chat_message_id = $chatMessage->id;
                        $store_replay->suggested_replay = $dialogFlowService->purifyResponse($response->getFulfillmentText(), $objectType == 'customer' ? $object : null);
                        $store_replay->type = $objectType;
                        $store_replay->type_id = $object->id;
                        $store_replay->save();
                    }
                }
            }
        } catch (Exception $e) {
            _p([$e->getMessage()]);
            exit;
        }
    }
}
