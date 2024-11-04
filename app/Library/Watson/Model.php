<?php

namespace App\Library\Watson;
use App\SuggestedProduct;
use App\Jobs\SendMessageToCustomer;
use App\Jobs\AttachSuggestionProduct;
use App\ChatbotReply;
use App\ChatbotMessageLogResponse;

use Illuminate\Support\Facades\Log;
use App\ChatMessage;
use App\ChatbotDialog;
use App\WatsonAccount;
use App\ChatbotKeyword;
use App\ChatbotErrorLog;
use App\ChatbotQuestion;
use App\WatsonWorkspace;
use App\Jobs\ManageWatson;
use App\ChatbotKeywordValue;
use App\ChatbotDialogErrorLog;
use App\ChatbotQuestionExample;
use App\Jobs\ManageWatsonAssistant;
use App\Library\Watson\Language\Workspaces\V1\LogService;
use App\Library\Watson\Language\Workspaces\V1\DialogService;
use App\Library\Watson\Language\Workspaces\V1\IntentService;
use App\Library\Watson\Language\Assistant\V2\AssistantService;
use App\Library\Watson\Language\Workspaces\V1\EntitiesService;

class Model
{
    const EXCLUDED_REPLY = [
        "Can you reword your statement? I'm not understanding.",
        "I didn't understand. You can try rephrasing.",
        "I didn't get your meaning.",
    ];

    const API_KEY = '9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC';

    public static function getWorkspaceId()
    {
        return '19cf3225-f007-4332-8013-74443d36a3f7';
    }

    public static function getAssistantId()
    {
        return '28754e1c-6281-42e6-82af-eec6e87618a6';
    }

    public static function pushKeyword($id)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }

        $keyword     = ChatbotKeyword::where('id', $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($keyword) {
            $storeParams                = [];
            $storeParams['entity']      = $keyword->keyword;
            $storeParams['fuzzy_match'] = true;
            $values                     = $keyword->chatbotKeywordValues()->get();
            $storeParams['values']      = [];
            $typeValue                  = [];

            foreach ($values as $value) {
                $typeValue = ChatbotKeywordValue::where('id', $value['id'])->first()->chatbotKeywordValueTypes()->get()->pluck('type');
                if ($value['types'] == 'synonyms') {
                    $storeParams['values'][] = ['value' => $value['value'], 'synonyms' => $typeValue];
                } else {
                    $storeParams['values'][] = ['value' => $value['value'], 'type' => 'patterns', 'patterns' => $typeValue];
                }
            }

            if (! empty($keyword->workspace_id)) {
                ManageWatson::dispatch('entity', $keyword, $storeParams, 'update')->onQueue('watson_push');
            } else {
                $keyword->workspace_id = $workSpaceId;
                $keyword->save();

                $wotson_account_ids = WatsonAccount::pluck('id')->toArray();

                foreach ($wotson_account_ids as $id) {
                    $data_to_insert[] = [
                        'type'              => 'ChatbotKeyword',
                        'watson_account_id' => $id,
                        'element_id'        => $keyword->id,
                    ];
                }

                WatsonWorkspace::insert($data_to_insert);

                ManageWatson::dispatch('entity', $keyword, $storeParams, 'create')->onQueue('watson_push');
            }
        }

        return true;
    }

    public static function deleteKeyword($id)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }

        $keyword = ChatbotKeyword::where('id', $id)->first();

        if (! empty($keyword) && ! empty($keyword->workspace_id)) {
            ManageWatson::dispatch('entity', $keyword, [], 'delete', 'keyword')->onQueue('watson_push');
        }

        return true;
    }

    public static function pushQuestion($id, $oldValue = null, $watson_account_id = null)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }

        $question    = ChatbotQuestion::where('id', $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($question) {
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

            if (! empty($question->workspace_id)) {
                ManageWatson::dispatch($question->keyword_or_question, $question, $storeParams, 'update', 'value', false, $oldValue)->onQueue('watson_push');
                ChatbotQuestion::where('id', $question->id)->update(['watson_status' => 'watson sended']);
            } else {
                $question->workspace_id = $workSpaceId;
                $question->save();

                if (! empty($watson_account_id)) {
                    $wotson_account_ids = WatsonAccount::where('id', $watson_account_id)->pluck('id')->toArray();
                } else {
                    $wotson_account_ids = WatsonAccount::pluck('id')->toArray();
                }

                foreach ($wotson_account_ids as $id) {
                    $data_to_insert[] = [
                        'type'              => 'ChatbotQuestion',
                        'watson_account_id' => $id,
                        'element_id'        => $question->id,
                    ];
                }

                WatsonWorkspace::insert($data_to_insert);
                ChatbotQuestion::where('id', $question->id)->update(['watson_status' => 'watson sended']);
                ManageWatson::dispatch($question->keyword_or_question, $question, $storeParams, 'create', 'value', false, $oldValue)->onQueue('watson_push');
            }
        }

        return true;
    }

    public static function pushQuestionSingleWebsite($id, $store_website_id)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }
        $success     = 0;
        $question    = ChatbotQuestion::where('id', $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($question) {
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
                            $sendMentions[] = [
                                'entity'   => $mRaw->chatbotKeyword->keyword,
                                'location' => [$mRaw->start_char_range, $mRaw->end_char_range],
                            ];
                        }
                        if (! empty($sendMentions)) {
                            $storeParams['examples'][$k]['mentions'] = $sendMentions;
                        }
                    }
                }
            }
            $account = WatsonAccount::where('store_website_id', $store_website_id)->first();
            if (! $account) {
                return false;
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
            if ($success == 1) {
                $lastError = ChatbotErrorLog::where('store_website_id', $store_website_id)->where('chatbot_question_id', $question->id)->where('status', 0)->orderByDesc('created_at')->first();
                if ($lastError) {
                    $lastError->update(['status' => 1, 'response' => json_encode($result)]);
                    ChatbotErrorLog::where('store_website_id', $store_website_id)->where('chatbot_question_id', $question->id)->where('status', 0)->where('id', '!=', $lastError->id)->delete();
                }
            }
        }

        return $success;
    }

    public static function pushValue($exampleId, $oldExample = '')
    {
        if (config('settings.push_watson') == false) {
            return true;
        }

        $questionExample = ChatbotQuestionExample::where('id', $exampleId)->first();
        $workSpaceId     = self::getWorkspaceId();

        if ($questionExample) {
            if (empty($oldExample)) {
                $oldExample = $questionExample->question;
            }

            $questionModel = $questionExample->questionModal;
            $question      = $questionExample->question;
            $mentions      = $questionExample->annotations;
            $storeParams   = [
                'text' => $questionExample->question,
            ];

            $sendMentions = [];
            if (! $mentions->isEmpty()) {
                foreach ($mentions as $key => $mRaw) {
                    if ($mRaw->chatbotKeyword) {
                        $sendMentions[] = [
                            'entity'   => $mRaw->chatbotKeyword->keyword,
                            'location' => [$mRaw->start_char_range, $mRaw->end_char_range],
                        ];
                    }
                }
            }

            if (! empty($sendMentions)) {
                $storeParams['mentions'] = $sendMentions;
            }
            if (! empty($questionModel->workspace_id)) {
                ManageWatson::dispatch('intent', $question, $storeParams, 'update_example', 'value', $oldExample)->onQueue('watson_push');
            }
        }

        return true;
    }

    public static function deleteQuestion($id)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }

        $question = ChatbotQuestion::where('id', $id)->first();

        if (! empty($question) && ! empty($question->workspace_id)) {
            ManageWatson::dispatch($question->keyword_or_question, $question, [], 'delete')->onQueue('watson_push');
        }

        return true;
    }

    public static function pushDialog($id, $suggested_reply = null, $reply_id = null)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }

        $dialog = ChatbotDialog::where('id', $id)->first();

        $workSpaceId = self::getWorkspaceId();

        if ($dialog) {
            $storeParams                = [];
            $storeParams['dialog_node'] = $dialog->name;
            $storeParams['conditions']  = $dialog->match_condition;
            $storeParams['title']       = $dialog->title;
            $values                     = $dialog->response;
            $storeParams['type']        = ($dialog->type == 'folder') ? 'folder' : 'standard';
            $storeParams['reply']       = $suggested_reply;
            $storeParams['reply_id']    = $reply_id;
            $genericOutput              = [];
            foreach ($values as $value) {
                $genericOutput['response_type'] = $value->response_type;
                $genericOutput['values'][]      = ['text' => $value->value];
            }

            if (! empty($dialog->workspace_id)) {
                $storeParams['output']['generic'][] = $genericOutput;
                ManageWatson::dispatch('dialog', $dialog, $storeParams, 'update', 'name')->onQueue('watson_push');
            } else {
                $dialog->workspace_id = $workSpaceId;
                $dialog->save();

                $wotson_account_ids = WatsonAccount::pluck('id')->toArray();

                foreach ($wotson_account_ids as $id) {
                    $data_to_insert[] = [
                        'type'              => 'ChatbotDialog',
                        'watson_account_id' => $id,
                        'element_id'        => $dialog->id,
                    ];
                }

                WatsonWorkspace::insert($data_to_insert);
                $storeParams['output']['generic'][] = $genericOutput;

                ManageWatson::dispatch('dialog', $dialog, $storeParams, 'create', 'name')->onQueue('watson_push');
            }
        }

        return true;
    }

    public static function deleteDialog($id)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }

        $dialog = ChatbotDialog::where('id', $id)->first();

        if (! empty($dialog) && ! empty($dialog->workspace_id)) {
            ManageWatson::dispatch('dialog', $dialog, [], 'delete', 'name')->onQueue('watson_push');
        }

        return true;
    }

    public static function sendMessage($customer, $inputText, $contextReset = false, $message_application_id = null, $messageModel = false, $userType = null, $chat_message_log_id = null)
    {
        if (isset($chat_message_log_id)) {
            ChatbotMessageLogResponse::StoreLogResponse([
                'chatbot_message_log_id' => $chat_message_log_id,
                'request'                => '',
                'response'               => 'Watson assistant function job dispatched initialize',
                'status'                 => 'success',
            ]);
        }

        ManageWatsonAssistant::dispatch($customer, $inputText, $contextReset, $message_application_id, $messageModel, $userType, $chat_message_log_id)->onQueue('watson_push');

        return true;
    }

    public static function sendMessageFromJob($customer, $account, $assistant, $inputText, $contextReset = false, $message_application_id = null, $messageModel = null, $userType = null, $chat_message_log_id = null)
    {
        if (config('settings.push_watson') == false) {
            if (isset($chat_message_log_id)) {
                ChatbotMessageLogResponse::StoreLogResponse([
                    'chatbot_message_log_id' => $chat_message_log_id,
                    'request'                => '',
                    'response'               => 'Watson assistant function push Watson functionality is disabled.',
                    'status'                 => 'success',
                ]);
            }

            return true;
        }

        $assistantID = $account->assistant_id;

        $chatbotReply = false;
        if ($messageModel) {
            $chatbotReply = ChatbotReply::where('replied_chat_id', $messageModel->id)->first();
            if (! $chatbotReply) {
                $chatbotReply = ChatbotReply::create([
                    'question'        => $inputText,
                    'replied_chat_id' => $messageModel->id,
                ]);
            }
        }

        if (empty($customer->chat_session_id)) {
            $customer = self::createSession($customer, $assistant, $assistantID);
            if (! $customer) {
                if (isset($chat_message_log_id)) {
                    ChatbotMessageLogResponse::StoreLogResponse([
                        'chatbot_message_log_id' => $chat_message_log_id,
                        'request'                => '',
                        'response'               => 'Watson assistant function create session customer not found.',
                        'status'                 => 'success',
                    ]);
                }

                return false;
            }
        }
        if (! empty($customer->chat_session_id)) {
            // now sending message to the watson

            $result = self::sendMessageCustomer($customer, $assistantID, $assistant, $inputText, $contextReset);

            if (isset($chat_message_log_id)) {
                ChatbotMessageLogResponse::StoreLogResponse([
                    'chatbot_message_log_id' => $chat_message_log_id,
                    'request'                => '',
                    'response'               => 'Watson assistant function send message customer function response found. => ' . json_encode($result),
                    'status'                 => 'success',
                ]);
            }

            if (! empty($result->code) && ($result->code == 403 || $result->code == 404)) {
                $customer = self::createSession($customer, $assistant, $assistantID);
                if ($customer) {
                    $result = self::sendMessageCustomer($customer, $assistantID, $assistant, $inputText, $contextReset);
                    if (isset($chat_message_log_id)) {
                        ChatbotMessageLogResponse::StoreLogResponse([
                            'chatbot_message_log_id' => $chat_message_log_id,
                            'request'                => '',
                            'response'               => 'Watson assistant function send message customer function response found. => ' . json_encode($result),
                            'status'                 => 'success',
                        ]);
                    }
                }
            }
            $chatResponse = new ResponsePurify($result, $customer);
            Log::channel('chatapi')->info(json_encode($chatResponse));
            // if response is valid then check ahead
            if (isset($chat_message_log_id)) {
                ChatbotMessageLogResponse::StoreLogResponse([
                    'chatbot_message_log_id' => $chat_message_log_id,
                    'request'                => '',
                    'response'               => 'Watson assistant function send message customer function response is valid. => ' . $chatResponse->isValid(),
                    'status'                 => 'success',
                ]);
            }

            if ($chatResponse->isValid()) {
                $result = $chatResponse->assignAction();
                Log::channel('chatapi')->info('##CHAT_ACTION## ' . json_encode($result));

                if (isset($chat_message_log_id)) {
                    ChatbotMessageLogResponse::StoreLogResponse([
                        'chatbot_message_log_id' => $chat_message_log_id,
                        'request'                => '',
                        'response'               => 'Watson assistant function assign action response. => ' . json_encode($result),
                        'status'                 => 'success',
                    ]);
                }

                if (! empty($result)) {
                    if (! empty($result['action'])) {
                        // assign params

                        $params = [
                            'is_queue'               => 0,
                            'status'                 => ChatMessage::CHAT_AUTO_WATSON_REPLY,
                            'customer_ids'           => [$customer->id],
                            'message'                => $result['reply_text'],
                            'is_chatbot'             => true,
                            'chatbot_response'       => $result,
                            'message_application_id' => $message_application_id,
                            'chatbot_question'       => $inputText,
                            'chatbot_params'         => isset($result['medias']) ? $result['medias'] : [],
                            'ticket_id'              => ! is_null($messageModel->ticket_id) ? $messageModel->ticket_id : null,
                        ];

                        switch ($result['action']) {
                            case 'send_product_images':

                                // add into suggestion
                                $brands   = [];
                                $category = [];

                                if (! empty($result['medias']['params']['brands'])) {
                                    $brands = $result['medias']['params']['brands'];
                                }

                                if (! empty($result['medias']['params']['category'])) {
                                    $category = $result['medias']['params']['category'];
                                }

                                self::sendMessageFromJob($customer, $account, $assistant, 'image_has_been_found', true);

                                if (! empty($brands) || ! empty($category)) {
                                    $suggestion = SuggestedProduct::create([
                                        'customer_id' => $customer->id,
                                        'brands'      => json_encode($brands),
                                        'categories'  => json_encode($category),
                                        'number'      => 30,
                                    ]);

                                    // setup the params
                                    $insertParams = [
                                        'customer_id'            => $customer->id,
                                        'message'                => isset($params['message']) ? $params['message'] : null,
                                        'status'                 => isset($params['status']) ? $params['status'] : ChatMessage::CHAT_AUTO_BROADCAST,
                                        'is_queue'               => isset($params['is_queue']) ? $params['is_queue'] : 0,
                                        'group_id'               => isset($params['group_id']) ? $params['group_id'] : null,
                                        'user_id'                => isset($params['user_id']) ? $params['user_id'] : null,
                                        'number'                 => null,
                                        'message_application_id' => $message_application_id,
                                        'is_chatbot'             => isset($params['is_chatbot']) ? $params['is_chatbot'] : 0,
                                        'is_email'               => (! empty($messageModel)) ? $messageModel->is_email : 0,
                                    ];

                                    $chatMessage = ChatMessage::create($insertParams);
                                    if ($chatMessage->status == ChatMessage::CHAT_AUTO_WATSON_REPLY) {
                                        if ($chatbotReply) {
                                            $chatbotReply->chat_id    = $chatMessage->id;
                                            $chatbotReply->answer     = $chatMessage->message;
                                            $chatbotReply->reply      = isset($params['chatbot_response']) ? json_encode($params['chatbot_response']) : null;
                                            $chatbotReply->reply_from = 'watson';
                                            $chatbotReply->save();
                                        }
                                    }

                                    $suggestion->chat_message_id = $chatMessage->id;
                                    $suggestion->save();

                                    AttachSuggestionProduct::dispatch($suggestion)->onQueue('customer_message');
                                }

                                break;
                            case 'send_text_only':
                                SendMessageToCustomer::dispatch($params, $chatbotReply)->onQueue('customer_message');
                                break;
                        }
                    }
                }
            }

            return false;
        }
    }

    public static function createSession($customer, AssistantService $assistant, $assistantID)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }

        $session = $assistant->createSession($assistantID);
        $result  = json_decode($session->getContent());
        if (isset($result->session_id)) {
            $customer->chat_session_id = $result->session_id;
            $customer->save();

            return $customer;
        }

        return false;
    }

    public static function createInstantSession(AssistantService $assistant, $assistantID)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }
        $session = $assistant->createSession($assistantID);
        $status  = $session->getStatusCode();
        $result  = json_decode($session->getContent());
        if ($status == 201 || $status == 200) {
            if ($result->session_id) {
                return $result->session_id;
            } else {
                return false;
            }
        }

        return false;
    }

    public static function getWatsonReply($message, $account_id)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }

        $account     = WatsonAccount::find($account_id);
        $assistantID = $account->assistant_id;
        $assistant   = new AssistantService(
            'apiKey',
            $account->api_key
        );
        $assistant->set_url($account->url);
        $session_id = self::createInstantSession($assistant, $assistantID);
        if (! $session_id) {
            return false;
        }

        $params = [
            'input' => [
                'text'    => $message,
                'options' => [
                    'return_context' => true,
                ],
            ],
        ];
        $result       = $assistant->sendMessage($assistantID, $session_id, $params);
        $result       = json_decode($result->getContent());
        $chatResponse = new ResponsePurify($result);
        if ($chatResponse->isValid()) {
            $responseData = $chatResponse->assignAction();
            if (! empty($responseData)) {
                if (! empty($responseData['reply_text'])) {
                    return $responseData['reply_text'];
                }
            }
        }

        return false;
    }

    public static function sendMessageCustomer($customer, $assistantID, AssistantService $assistant, $inputText, $contextReset = false)
    {
        if (config('settings.push_watson') == false) {
            return true;
        }

        $params = [
            'input' => [
                'text'    => $inputText,
                'options' => [
                    'return_context' => true,
                ],
            ],
        ];
        $result = $assistant->sendMessage($assistantID, $customer->chat_session_id, $params);

        return json_decode($result->getContent());
    }

    public static function newPushDialogSingle($id, $store_website_id)
    {
        if (config('settings.push_watson') == false) {
            return ['code' => 500, 'error' => 'Sorry, Watson push is not activated'];
        }

        $dialog      = ChatbotDialog::where('id', $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($dialog) {
            $storeParams                     = [];
            $storeParams['dialog_node']      = $dialog->name;
            $storeParams['conditions']       = $dialog->match_condition;
            $storeParams['title']            = $dialog->title;
            $storeParams['previous_sibling'] = $dialog->getPreviousSiblingName();
            $storeParams['type']             = ($dialog->dialog_type == 'folder') ? $dialog->dialog_type : $dialog->response_type;
            $storeParams['parent']           = $dialog->getParentName();

            $multipleResponse = false;
            if (! empty($dialog->metadata) && $storeParams['type'] != 'folder') {
                $multipleResponse = true;
            }

            $genericOutput = [];
            if (! $multipleResponse) {
                $wbsiteRes = $dialog->response()->where('store_website_id', $store_website_id)->get();
                foreach ($wbsiteRes as $value) {
                    $genericOutput['response_type'] = $value->response_type;
                    $genericOutput['values'][]      = ['text' => $value->value];
                }
            }

            if (! empty($genericOutput) && $storeParams['type'] != 'folder') {
                $storeParams['output']['generic'][] = $genericOutput;
            }

            if (! empty($dialog->workspace_id)) {
                self::dialogPushToWatson($dialog, $storeParams, 'update', $store_website_id);
            } else {
                $dialog->workspace_id = $workSpaceId;
                $dialog->save();
                self::dialogPushToWatson($dialog, $storeParams, 'create', $store_website_id);
            }
            if ($multipleResponse) {
                $multipleDialog = $dialog->multipleCondition()->where('response_type', 'response_condition')->get();
                if (! $multipleDialog->isEmpty()) {
                    foreach ($multipleDialog as $mulDialog) {
                        $storeParams                     = [];
                        $storeParams['dialog_node']      = $mulDialog->name;
                        $storeParams['conditions']       = $mulDialog->match_condition;
                        $storeParams['title']            = $mulDialog->title;
                        $storeParams['previous_sibling'] = $mulDialog->getPreviousSiblingName();
                        $storeParams['type']             = $mulDialog->response_type;
                        $storeParams['parent']           = $mulDialog->getParentName();

                        $genericOutput = [];
                        $wbsiteRes     = $mulDialog->response()->where('store_website_id', $store_website_id)->get();
                        foreach ($wbsiteRes as $value) {
                            $genericOutput['response_type'] = $value->response_type;
                            $genericOutput['values'][]      = ['text' => $value->value];
                        }

                        if (! empty($mulDialog->workspace_id)) {
                            $storeParams['output']['generic'][] = $genericOutput;
                            self::dialogPushToWatson($mulDialog, $storeParams, 'update', $store_website_id);
                        } else {
                            $storeParams['output']['generic'][] = $genericOutput;
                            $mulDialog->workspace_id            = $workSpaceId;
                            $mulDialog->save();

                            self::dialogPushToWatson($mulDialog, $storeParams, 'create', $store_website_id);
                        }
                    }
                }
            }

            return ['code' => 200, 'error' => false];
        }
    }

    public static function dialogPushToWatson($dialog, array $storeParams, $method, $store_website_id)
    {
        $account = WatsonAccount::where('store_website_id', $store_website_id)->first();
        if (! $account) {
            return false;
        }
        $value        = $dialog->name;
        $serviceClass = 'DialogService';
        $watson       = new DialogService(
            'apiKey',
            $account->api_key
        );
        $watson->set_url($account->url);
        $result = $watson->update($account->work_space_id, $value, $storeParams);
        $status = $result->getStatusCode();
        if ($status == 404) {
            $rs     = $watson->create($account->work_space_id, $storeParams);
            $status = $rs->getStatusCode();
        }
        if ($status == 201 || $status == 200) {
            $success = 1;
        } else {
            $success = 0;
        }
        $errorlog                    = new ChatbotDialogErrorLog;
        $errorlog->chatbot_dialog_id = $dialog->id;
        $errorlog->store_website_id  = $account->store_website_id;
        $errorlog->status            = $success;
        $errorlog->response          = $result->getContent();
        $errorlog->save();

        return true;
    }

    public static function newPushDialog($id)
    {
        if (config('settings.push_watson') == false) {
            return ['code' => 500, 'error' => 'Sorry, Watson push is not activated'];
        }

        $dialog      = ChatbotDialog::where('id', $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($dialog) {
            $storeParams                     = [];
            $storeParams['dialog_node']      = $dialog->name;
            $storeParams['conditions']       = $dialog->match_condition;
            $storeParams['title']            = $dialog->title;
            $storeParams['previous_sibling'] = $dialog->getPreviousSiblingName();
            $storeParams['type']             = ($dialog->dialog_type == 'folder') ? $dialog->dialog_type : $dialog->response_type;
            $storeParams['parent']           = $dialog->getParentName();

            $multipleResponse = false;
            if (! empty($dialog->metadata) && $storeParams['type'] != 'folder') {
                $multipleResponse = true;
            }

            $genericOutput = [];
            if (! $multipleResponse) {
                foreach ($dialog->response as $value) {
                    $genericOutput['response_type'] = $value->response_type;
                    $genericOutput['values'][]      = ['text' => $value->value];
                }
            }
            if (! empty($genericOutput) && $storeParams['type'] != 'folder') {
                $storeParams['output']['generic'][] = $genericOutput;
            }

            if (! empty($dialog->workspace_id)) {
                ManageWatson::dispatch('dialog', $dialog, $storeParams, 'update', 'name')->onQueue('watson_push');
            } else {
                $dialog->workspace_id = $workSpaceId;
                $dialog->save();
                ManageWatson::dispatch('dialog', $dialog, $storeParams, 'create', 'name')->onQueue('watson_push');
            }

            // once stored into the api now we will check for the multiple response condition
            if ($multipleResponse) {
                $multipleDialog = $dialog->multipleCondition()->where('response_type', 'response_condition')->get();
                if (! $multipleDialog->isEmpty()) {
                    foreach ($multipleDialog as $mulDialog) {
                        $storeParams                     = [];
                        $storeParams['dialog_node']      = $mulDialog->name;
                        $storeParams['conditions']       = $mulDialog->match_condition;
                        $storeParams['title']            = $mulDialog->title;
                        $storeParams['previous_sibling'] = $mulDialog->getPreviousSiblingName();
                        $storeParams['type']             = $mulDialog->response_type;
                        $storeParams['parent']           = $mulDialog->getParentName();

                        $genericOutput = [];
                        foreach ($mulDialog->response as $value) {
                            $genericOutput['response_type'] = $value->response_type;
                            $genericOutput['values'][]      = ['text' => $value->value];
                        }

                        if (! empty($mulDialog->workspace_id)) {
                            $storeParams['output']['generic'][] = $genericOutput;
                            ManageWatson::dispatch('dialog', $mulDialog, $storeParams, 'update', 'name')->onQueue('watson_push');
                        } else {
                            $storeParams['output']['generic'][] = $genericOutput;
                            $mulDialog->workspace_id            = $workSpaceId;
                            $mulDialog->save();

                            ManageWatson::dispatch('dialog', $mulDialog, $storeParams, 'create', 'name')->onQueue('watson_push');
                        }
                    }
                }
            }

            return ['code' => 200, 'error' => false];
        }
    }

    public static function getLog($params = [])
    {
        $log = new LogService(
            'apiKey',
            self::API_KEY
        );

        $response = $log->get(self::getWorkspaceId(), $params);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getContent());
        }

        return [];
    }
}
