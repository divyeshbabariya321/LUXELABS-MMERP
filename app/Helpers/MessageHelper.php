<?php

namespace App\Helpers;

use App\AutoReply;
use App\ChatbotMessageLogResponse;
use App\ChatbotQuestion;
use App\ChatbotReply;
use App\ChatMessage;
use App\ChatMessagesQuickData;
use App\CommunicationHistory;
use App\Email;
use App\ErpLeads;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\SocialAccountCommentController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\WhatsAppController;
use App\Instruction;
use App\Keywordassign;
use App\KeywordAutoGenratedMessageLog;
use App\Leads;
use App\Library\Watson\Model as WatsonManager;
use App\Mediables;
use App\MessageQueue;
use App\Models\SocialComments;
use App\Product;
use App\Services\ChatbotService;
use App\Task;
use App\TaskUser;
use App\Tickets;
use App\User;
use App\WatsonChatJourney;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageHelper
{
    const TXT_ARTICLES = [
        'a',
        'an',
        'the',
    ];

    const AUTO_LEAD_SEND_PRICE = 281;

    const AUTO_DIMENSION_SEND = 7;

    const TXT_PREPOSITIONS = [
        'aboard',
        'about',
        'above',
        'across',
        'after',
        'against',
        'along',
        'amid',
        'among',
        'anti',
        'around',
        'as',
        'at',
        'before',
        'behind',
        'below',
        'beneath',
        'beside',
        'besides',
        'between',
        'beyond',
        'but',
        'by',
        'concerning',
        'considering',
        'despite',
        'down',
        'during',
        'except',
        'excepting',
        'excluding',
        'following',
        'for',
        'from',
        'in',
        'inside',
        'into',
        'like',
        'minus',
        'near',
        'of',
        'off',
        'on',
        'onto',
        'opposite',
        'outside',
        'over',
        'past',
        'per',
        'plus',
        'regarding',
        'round',
        'save',
        'since',
        'than',
        'through',
        'to',
        'toward',
        'towards',
        'under',
        'underneath',
        'unlike',
        'until',
        'up',
        'upon',
        'versus',
        'via',
        'with',
        'within',
        'without',
    ];

    public static function getMostUsedWords()
    {
        $chatMessages = ChatMessage::where('customer_id', '>', 0)
            ->where('message', '!=', '')
            ->whereNotNull('number')
            ->select('message', 'id')->groupBy('message')->get()->pluck('message', 'id');

        //rows here should be replaced by the SQL result
        $wordTotals = [];
        $phrases = [];
        foreach ($chatMessages as $id => $row) {
            $words = explode(' ', $row);
            foreach ($words as $word) {
                if (! in_array($word, self::TXT_ARTICLES + self::TXT_PREPOSITIONS) && $word != '') {
                    $phrases[$word][] = ['txt' => $row, 'id' => $id];
                    if (isset($wordTotals[$word])) {
                        $wordTotals[$word]++;

                        continue;
                    }

                    $wordTotals[$word] = 1;
                }
            }
        }

        arsort($wordTotals);

        $records = [];
        foreach ($wordTotals as $word => $count) {
            $records['words'][$word] = [
                'word' => $word,
                'total' => $count,
            ];

            $records['phrases'][$word] = [
                'phrases' => isset($phrases[$word]) ? $phrases[$word] : [],
            ];
        }

        return $records;
    }

    public static function sendGeminiAiReply($inputQuery, $msgType, $messageData, $storeWebsite, $customer = null, $emailData = null)
    {
        $chatbotService = new ChatbotService($storeWebsite);
        $response = $chatbotService->getAutomateReply($inputQuery);
        $replyData = json_decode($response->getContent(), true);
        if (! $replyData['status']) {
            Log::info('There was an issue while sending automatic reply'.$replyData['message']);
            return false;
        }
        $messageApprovedToSend = $chatbotService->fallbackMessage !== $replyData['message'];

        // Common ChatbotReply parameters
        $chatbotReplyParams = [
            'question'         => $inputQuery,
            'answer'           => $replyData['message'],
            'replied_chat_id'  => $messageData->id,
            'reply_from'       => 'Gemini AI',
            'reply'            => json_encode($replyData),
            'suggested_reply'  => !$messageApprovedToSend ? $replyData['message'] : null,
        ];
        $chatMessagesParams = [
            'message' => $replyData['message'],
            'message_type' => $messageData->message_type,
            'message_type_id' => $messageData->message_type_id,
        ];
        switch ($msgType) {
            case 'EMAIL':
                $email = Email::findOrFail($messageData->email_id);
                $request = new Request([
                    'receiver_email' => $email->to,
                    'subject'        => $email->subject,
                    'message'        => $replyData['message'],
                    'reply_email_id' => $messageData->email_id,
                ]);
                (new EmailController)->submitReply($request);
                return true;
            case 'Ticket':
                $ticket = Tickets::find($messageData->ticket_id);
                $chatMessagesParams['message'] = $replyData['message'];
                $messageScore = null;
                if ($ticket) {
                    if (!empty($ticket->lang_code) && $ticket->lang_code != 'en') {
                        $chatMessagesParams['message'] = TranslationHelper::translate('en', $ticket->lang_code, $chatMessagesParams['message']);
                        $messageScore = app('translation-lambda-helper')->getTranslateScore($replyData['message'], $chatMessagesParams['message']);
                    }
                    $chatMessagesParams['customer_id'] = $ticket->customer_id;
                }
                if ($messageScore) {
                    $chatMessagesParams['message_score'] = $messageScore;
                }
                $chatMessagesParams['message_en'] = $replyData['message'];
                $chatMessagesParams['ticket_id'] = $messageData->ticket_id;
                $chatMessagesParams['approved'] = 1;
                $chatMessagesParams['status'] = 2;
                if ($messageApprovedToSend) {
                    $chatMessagesParams['send_to_tickets'] = 1;
                }
                ChatMessage::create($chatMessagesParams);
                ChatbotReply::create($chatbotReplyParams);
                return true;
            case 'COMMENT':
                $socialComment = SocialComments::find($messageData->message_type_id);
                if ($socialComment && $socialComment->can_comment) {
                    ChatMessage::create($chatMessagesParams);
                    if ($messageApprovedToSend) {
                        $formData = [
                            'input' => $replyData['message'],
                            'contactId' => $socialComment->id,
                            'configId' => $socialComment->post->config_id,
                        ];
                        $request = new Request($formData);
                        $socialAccountCommentController = new SocialAccountCommentController;
                        $socialAccountCommentController->devCommentsReply($request);
                    }
                    ChatbotReply::create($chatbotReplyParams);
                    return true;
                }
                return false;
            case 'DMS':
                ChatMessage::create($chatMessagesParams);
                if ($messageApprovedToSend) {
                    $formData = [
                        'input' => $replyData['message'],
                        'contactId' => $messageData->message_type_id,
                    ];
                    $request = new Request($formData);
                    $socialAccountController = new SocialAccountController;
                    $socialAccountController->sendMessage($request);
                }
                ChatbotReply::create($chatbotReplyParams);
                return true;
            case 'TWILIO':
                ChatbotReply::create($chatbotReplyParams);
                return true;
        }

        return false;
    }

    /**
     * Send whatsApp Message
     *
     * @param  $customer  [ object ]
     * @param  $message  [ string ]
     * @param  null|mixed  $sendMsg
     * @param  null|mixed  $messageModel
     * @param  null|mixed  $isEmail
     * @param  null|mixed  $parentMessage
     * @return mixed
     */
    public static function whatsAppSend($customer = null, $message = null, $sendMsg = null, $messageModel = null, $isEmail = null, $parentMessage = null)
    {
        $j = 0;
        if ($customer) {
            $temp_log_params = ['keyword' => '', 'keyword_match' => ''];
            //START - Purpose : Add Data in array - DEVTASK-4233
            $log_comment = 'whatsAppSend : ';
            if (! empty($messageModel)) {
                $temp_log_params['model'] = $messageModel->getTable();
                $temp_log_params['model_id'] = $messageModel->id;
            }
            //END - DEVTASK-4233

            $exp_mesaages = explode(' ', $message);

            $temp_log_params['keyword'] = implode(', ', $exp_mesaages); //Purpose : Add keyword in array - DEVTASK-4233

            for ($i = 0; $i < count($exp_mesaages); $i++) {
                $keywordassign = Keywordassign::select('*')
                    ->whereRaw('FIND_IN_SET(?,keyword)', [strtolower($exp_mesaages[$i])])
                    ->get();

                if (count($keywordassign) > 0) {
                    $log_comment = $log_comment.' Keyword is '.$exp_mesaages[$i];
                    $temp_log_params['keyword_match'] = $keywordassign[0]->task_description;
                    $j++;
                    break;
                }
            }
            if ($j == 0) {
                $log_comment = $log_comment.' Not any keyword found';
            }

            Log::info('Keyword assign found'.count($keywordassign));

            if (count($keywordassign) > 0) {
                //START - Purpose : Log Comment - DEVTASK-4233
                $log_comment = $log_comment.' and Keyword match Description is '.$keywordassign[0]->task_description.', ';

                $temp_log_params['keyword_match'] = $keywordassign[0]->task_description;
                //END - DEVTASK-4233

                $task_array = [
                    'category' => 42,
                    'is_statutory' => 0,
                    'task_subject' => '#'.$customer->id.'-'.$keywordassign[0]->task_description,
                    'task_details' => $keywordassign[0]->task_description,
                    'assign_from' => User::USER_ADMIN_ID,
                    'assign_to' => $keywordassign[0]->assign_to,
                    'customer_id' => $customer->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                Task::insert($task_array);
                $taskid = DB::getPdo()->lastInsertId();
                $task_users_array = [
                    'task_id' => $taskid,
                    'user_id' => $keywordassign[0]->assign_to,
                    'type' => User::class,
                ];
                TaskUser::insert($task_users_array);

                $log_comment = $log_comment.' Keyword is found and task has been created with ID : '.$taskid;

                // check that match if this the assign to is auto user
                // then send price and deal
                Log::channel('whatsapp')->info('Price lead section has been started for customer with ID : '.$customer->id);

                if ($keywordassign[0]->assign_to == self::AUTO_LEAD_SEND_PRICE) {
                    Log::channel('whatsapp')->info('Auto Lend Send Price has been started for the customer with ID : '.$customer->id);
                    $log_comment = $log_comment.'Auto Lend Send Price has been started for the customer with ID : '.$customer->id; //Purpose : Log Comment - DEVTASK-4233

                    if (! empty($parentMessage)) {
                        Log::channel('whatsapp')->info('Auto Lend Send Price parent message with lead price has been found for customer with ID : '.$customer->id);

                        $log_comment = $log_comment.' Auto Lend Send Price parent message with lead price has been found for customer with ID : '.$customer->id; //Purpose : Log Comment - DEVTASK-4233

                        $parentMessage->sendLeadPrice($customer, $log_comment);
                    } else {
                        $log_comment = $log_comment.'Auto Lend Send Price parent message with lead price has not been found for customer with ID : '.$customer->id; //Purpose : Log Comment - DEVTASK-4233
                    }
                } elseif ($keywordassign[0]->assign_to == self::AUTO_DIMENSION_SEND) {
                    Log::channel('whatsapp')->info('Auto Dimension Send has been started for the customer with ID : '.$customer->id);
                    if (! empty($parentMessage)) {
                        Log::channel('whatsapp')->info('Auto Dimension Send parent message with lead price has been found for customer with ID : '.$customer->id);

                        $log_comment = $log_comment.' Auto Dimension Send parent message with lead price has been found for customer with ID : '.$customer->id.' >> '; //Purpose : Log Comment - DEVTASK-4233

                        $products = Leads::select('*')
                            ->where('id', '=', $parentMessage->lead_id)
                            ->get();
                        if (! empty($products[0]->selected_product)) {
                            $requestData = new Request;
                            $requestData->setMethod('POST');
                            $requestData->request->add(['customer_id' => $customer->id, 'dimension' => true, 'selected_product' => $products[0]->selected_product]);

                            app(LeadsController::class)->sendPrices($requestData, new GuzzleClient);
                        }
                    } else {
                        $log_comment = $log_comment.' Auto Dimension Send parent message with lead price has not been found for customer with ID : '.$customer->id.' >> '; //Purpose : Log Comment - DEVTASK-4233
                    }
                } else {
                    $log_comment = $log_comment.' Keyword assign is not matching with Auto Lend Send Price or Auto Dimension Send ';
                }

                //START CODE Task message to send message in whatsapp

                $task_info = Task::select('*')
                    ->where('id', '=', $taskid)
                    ->get();

                $users_info = User::select('*')
                    ->where('id', '=', $task_info[0]->assign_to)
                    ->get();

                if (count($users_info) > 0) {
                    if ($users_info[0]->phone != '') {
                        //START - Purpose : Log Comment - DEVTASK-4233
                        $log_comment = $log_comment.' User Info id : '.$users_info[0]->id.' and ';
                        $log_comment = $log_comment.' User Info phone : '.$users_info[0]->phone.' Send Whatsapp Message ';
                        //END - DEVTASK-4233

                        $params_task = [
                            'number' => null,
                            'user_id' => $users_info[0]->id,
                            'approved' => 1,
                            'status' => 2,
                            'task_id' => $taskid,
                            'message' => $task_info[0]->task_details,
                            'quoted_message_id' => ($messageModel) ? $messageModel->quoted_message_id : null,
                        ];

                        if ($sendMsg === true) {
                            app(WhatsAppController::class)->sendWithThirdApi($users_info[0]->phone, $users_info[0]->whatsapp_number, $task_info[0]->task_details);
                        }

                        $chat_message = ChatMessage::create($params_task);
                        ChatMessagesQuickData::updateOrCreate([
                            'model' => Task::class,
                            'model_id' => $taskid,
                        ], [
                            'last_communicated_message' => $task_info[0]->task_details,
                            'last_communicated_message_at' => $chat_message->created_at,
                            'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
                        ]);

                        $myRequest = new Request;
                        $myRequest->setMethod('POST');
                        $myRequest->request->add(['messageId' => $chat_message->id]); //Purpose : add messageid in array - DEVTASK-4233

                        $log_comment = $log_comment.' and task has been created with ID : '.$taskid;

                        $temp_log_params['message_sent_id'] = $chat_message->id;

                        if ($sendMsg === true) {
                            app(WhatsAppController::class)->approveMessage('task', $myRequest);
                        }
                    }
                } else {
                    $log_comment = $log_comment.' User not found ';
                }
                //END CODE Task message to send message in whatsapp
            } else {
                $log_comment = $log_comment.' Keyword not found ';
            }

            //START - Purpose : Log Comment , add data - DEVTASK-4233
            $log_comment = $log_comment.' . ';
            $temp_log_params['comment'] = $log_comment;

            if (! empty($temp_log_params['keyword_match']) && $temp_log_params['keyword_match'] != '') {
                $add_keyword = KeywordAutoGenratedMessageLog::create($temp_log_params);
            }
            //END - DEVTASK-4233
        }
    }

    /**
     * Send watson reply
     *
     * @param  $Custmoer  [ Object ]
     * @param  $message  [ string ]
     * @param  null|mixed  $customer
     * @param  null|mixed  $sendMsg
     * @param  null|mixed  $messageModel
     * @param  mixed  $params
     * @param  null|mixed  $isEmail
     * @param  null|mixed  $userType
     * @return mixed
     */
    public static function sendwatson($customer = null, $message = null, $sendMsg = null, $messageModel = null, $params = [], $isEmail = null, $userType = null)
    {
        //START - Purpose : add data in array - DEVTASK-4233
        $log_comment = 'sendwatson : ';
        if (! empty($messageModel)) {
            $temp_log_params['model'] = $messageModel->getTable();
            $temp_log_params['model_id'] = $messageModel->id;
        }
        //END - DEVTASK-4233

        if (isset($params['chat_message_log_id'])) {
            ChatbotMessageLogResponse::StoreLogResponse([
                'chatbot_message_log_id' => $params['chat_message_log_id'],
                'request' => '',
                'response' => 'Send watson message function started',
                'status' => 'success',
            ]);
        } else {
            $log_comment = $log_comment.' Chat message log ID not found and no entry has been created for chat message ';
        }

        $isReplied = 0;
        if ($userType !== 'vendor') {
            $log_comment = $log_comment.' User Type is not Vendor '; //Purpose : Log Comment - DEVTASK-4233
            Log::info('#2 Price for customer vendor condition passed');
            if (preg_match('/price/i', $message) || preg_match('/you photo/i', $message) || preg_match('/pp/i', $message) || preg_match('/how much/i', $message) || preg_match('/cost/i', $message) || preg_match('/rate/i', $message)) {
                //START - Purpose : Log Comment , get task discription - DEVTASK-4233
                // $log_comment = $log_comment.' Keyword Match >> ';

                $exp_mesaages = explode(' ', $message);
                $temp_log_params['keyword'] = implode(', ', $exp_mesaages);

                for ($i = 0; $i < count($exp_mesaages); $i++) {
                    $keywordassign = Keywordassign::select('*')
                        ->whereRaw('FIND_IN_SET(?,keyword)', [strtolower($exp_mesaages[$i])])
                        ->get();
                    if (count($keywordassign) > 0) {
                        $log_comment = $log_comment.' Keyword is '.$exp_mesaages[$i];
                        break;
                    }
                }

                if (isset($params['chat_message_log_id'])) {
                    ChatbotMessageLogResponse::StoreLogResponse([
                        'chatbot_message_log_id' => $params['chat_message_log_id'],
                        'request' => '',
                        'response' => 'Keyword assign match section started',
                        'status' => 'success',
                    ]);
                }

                if (count($keywordassign) > 0) {
                    // $log_comment = $log_comment.' Keyword assign found >> ';
                    $log_comment = $log_comment.' and Keyword match Description is '.$keywordassign[0]->task_description.', ';

                    $temp_log_params['keyword_match'] = $keywordassign[0]->task_description;

                    if (isset($params['chat_message_log_id'])) {
                        ChatbotMessageLogResponse::StoreLogResponse([
                            'chatbot_message_log_id' => $params['chat_message_log_id'],
                            'request' => '',
                            'response' => 'Keyword assign match found : '.$keywordassign[0]->task_description,
                            'status' => 'success',
                        ]);
                    }
                }
                //END - DEVTASK-4233

                Log::info('#3 Price for customer message condition passed');
                if ($customer) {
                    Log::info('#4 Price for customer model passed');

                    $log_comment = $log_comment.' Customerid is '.$customer->id; //Purpose : Log Comment - DEVTASK-4233
                    // send price from meessage queue
                    $messageSentLast = MessageQueue::where('customer_id', $customer->id)->where('sent', 1)->orderByDesc('sending_time')->first();
                    // if message found then start
                    $selected_products = [];
                    if ($messageSentLast) {
                        $mqProducts = $messageSentLast->getImagesWithProducts();
                        if (! empty($mqProducts)) {
                            foreach ($mqProducts as $mq) {
                                if (! empty($mq['products'])) {
                                    foreach ($mq['products'] as $productId) {
                                        $selected_products[] = $productId;
                                    }
                                }
                            }
                        }
                    }

                    // check the last message send for price
                    $lastChatMessage = ChatMessage::getLastImgProductId($customer->id);
                    if ($lastChatMessage) {
                        Log::info('#5 last message condition found'.$lastChatMessage->id);

                        $log_comment = $log_comment.' and get Last message id from ChatMessage id is '.$lastChatMessage->id.' '; //Purpose : Log Comment - DEVTASK-4233

                        if ($lastChatMessage->hasMedia(config('constants.attach_image_tag'))) {
                            Log::info('#6 last message has media found');
                            $lastImg = $lastChatMessage->getMedia(config('constants.attach_image_tag'))->sortByDesc('id')->first();
                            Log::info('#7 last message get media found');
                            if ($lastImg) {
                                Log::info('#8 last message media found '.$lastImg->id);

                                $log_comment = $log_comment.' Last Message Media Found : '.$lastImg->id.' ,'; //Purpose : Log Comment - DEVTASK-4233

                                $mediable = Mediables::where('media_id', $lastImg->id)->where('mediable_type', Product::class)->first();
                                if (! empty($mediable)) {
                                    Log::info('#9 last message mediable found');

                                    $product = Product::find($mediable->mediable_id);
                                    if (! empty($product)) {
                                        Log::info('#9 last message product found');
                                        $priceO = ($product->price_inr_special > 0) ? $product->price_inr_special : $product->price_inr;
                                        $selected_products[] = $product->id;
                                        $temp_img_params = $params;
                                        $temp_img_params['message'] = 'Price : '.$priceO;
                                        $temp_img_params['media_url'] = null;
                                        $temp_img_params['status'] = 2;
                                        $temp_img_params['is_email'] = ($isEmail == 1) ? 1 : 0;
                                        $temp_img_params['is_draft'] = ($isEmail == 1) ? 1 : 0;
                                        // Create new message
                                        ChatMessage::create($temp_img_params);

                                        if (isset($params['chat_message_log_id'])) {
                                            $data = [
                                                'chatbot_message_log_id' => $params['chat_message_log_id'],
                                                'request' => '',
                                                'response' => 'Chat Message is created.',
                                                'status' => 'success',
                                            ];
                                            $chat_message_log = ChatbotMessageLogResponse::StoreLogResponse($data);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (! empty($selected_products) && $messageSentLast) {
                        foreach ($selected_products as $pid) {
                            $product = Product::where('id', $pid)->first();
                            $quick_lead = ErpLeads::create([
                                'customer_id' => $customer->id,
                                'lead_status_id' => 3,
                                'product_id' => $pid,
                                'brand_id' => $product ? $product->brand : null,
                                'category_id' => $product ? $product->category : null,
                                'brand_segment' => $product && $product->brands ? $product->brands->brand_segment : null,
                                'color' => $customer->color,
                                'size' => $customer->size,
                                'type' => 'send-watson-reply',
                                'created_at' => Carbon::now(),
                            ]);

                            $log_comment = $log_comment.' Create ERP lead lead id is '.$quick_lead->id; //Purpose : Log Comment - DEVTASK-4233
                            if (isset($params['chat_message_log_id'])) {
                                $data = [
                                    'chatbot_message_log_id' => $params['chat_message_log_id'],
                                    'request' => '',
                                    'response' => 'Erp Lead is generated.',
                                    'status' => 'success',
                                ];
                                $chat_message_log = ChatbotMessageLogResponse::StoreLogResponse($data);
                            }
                        }

                        if (isset($params['chat_message_log_id'])) {
                            $data = [
                                'chatbot_message_log_id' => $params['chat_message_log_id'],
                                'request' => '',
                                'response' => 'Lead price send to customer.',
                                'status' => 'success',
                            ];
                            $chat_message_log = ChatbotMessageLogResponse::StoreLogResponse($data);
                        }

                        $requestData = new Request;
                        $requestData->setMethod('POST');
                        $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $selected_products]);

                        $response = app(LeadsController::class)->sendPrices($requestData, new GuzzleClient);

                        if (isset($params['chat_message_log_id'])) {
                            $data = [
                                'chatbot_message_log_id' => $params['chat_message_log_id'],
                                'request' => $requestData,
                                'response' => $response,
                                'status' => 'success',
                            ];
                            $chat_message_log = ChatbotMessageLogResponse::StoreLogResponse($data);
                        }

                        CommunicationHistory::create([
                            'model_id' => $messageSentLast->id,
                            'model_type' => MessageQueue::class,
                            'type' => 'broadcast-prices',
                            'method' => 'email',
                        ]);
                    }

                    Instruction::create([
                        'customer_id' => $customer->id,
                        'instruction' => 'Please send the prices',
                        'category_id' => 1,
                        'assigned_to' => 7,
                        'assigned_from' => 6,
                    ]);
                }
            } else {
                $log_comment = $log_comment.' Price and auto keyword not matched ';
            }
        }

        if (! empty($message)) {
            WatsonChatJourney::updateOrCreate(['chat_message_id' => $messageModel->id], ['chat_entered' => 1]);
            WatsonChatJourney::updateOrCreate(['chat_message_id' => $messageModel->id], ['message_received' => $message]);

            // auto mated reply here
            $auto_replies = AutoReply::where('is_active', 1)->get();
            if (! $auto_replies->isEmpty()) {
                foreach ($auto_replies as $auto_reply) {
                    if (preg_match("/{$auto_reply->keyword}/i", strtolower(trim($message))) && $auto_reply->reply) {
                        $temp_params = $params;
                        $temp_params['message'] = $auto_reply->reply;
                        $temp_params['media_url'] = null;
                        $temp_params['status'] = 2;
                        $temp_params['approved'] = 1;
                        // Create new message
                        $messageModel = ChatMessage::create($temp_params);

                        $log_comment = $log_comment.' , If Empty message found then Create Auto Mated reply in ChatMessage Table with ID : '.$messageModel->id; //Purpose : Log Comment - DEVTASK-4233
                        if (isset($params['chat_message_log_id'])) {
                            $data = [
                                'chatbot_message_log_id' => $params['chat_message_log_id'],
                                'request' => '',
                                'response' => 'Empty message received then automated reply from ChatMessage table',
                                'status' => 'success',
                            ];
                            $chat_message_log = ChatbotMessageLogResponse::StoreLogResponse($data);
                        } else {
                            $log_comment = $log_comment.' Chat message log ID not found ';
                        }
                    }
                }
            } else {
                $log_comment = $log_comment.' Auto replies are not found ';
            }

            $replies = ChatbotQuestion::join('chatbot_question_examples', 'chatbot_questions.id', 'chatbot_question_examples.chatbot_question_id')
                ->join('chatbot_questions_reply', 'chatbot_questions.id', 'chatbot_questions_reply.chatbot_question_id')
                ->where('chatbot_questions_reply.store_website_id', ($customer->store_website_id) ? $customer->store_website_id : 1)
                ->select('chatbot_questions.value', 'chatbot_questions.keyword_or_question', 'chatbot_questions.erp_or_watson', 'chatbot_questions.auto_approve', 'chatbot_question_examples.question', 'chatbot_questions_reply.suggested_reply')
                ->where('chatbot_questions.erp_or_watson', 'erp')
                ->get();

            if ($messageModel) {
                $chatbotReply = ChatbotReply::create([
                    'question' => $message,
                    'replied_chat_id' => $messageModel->id,
                ]);

                //START - Purpose : Log Comment , Add message sent id in array - DEVTASK-4233
                $temp_log_params['message_sent_id'] = $messageModel->id;

                // $log_comment = $log_comment.' Chat Message Create : '.$messageModel->id.'  >> ';
                //END - DEVTASK-4233

                foreach ($replies as $reply) {
                    if ($message != '' && $customer) {
                        $keyword = $reply->question;
                        if (($keyword == $message || strpos(strtolower(trim($message)), strtolower(trim($keyword))) !== false) && $reply->suggested_reply) {
                            $status = ChatMessage::CHAT_AUTO_WATSON_REPLY;
                            $temp_params = $params;
                            $temp_params['message'] = $reply->suggested_reply;
                            $temp_params['media_url'] = null;
                            $temp_params['status'] = $status;
                            $temp_params['question_id'] = $reply->id;

                            // Create new message
                            $message = ChatMessage::create($temp_params);

                            if ($message->status == ChatMessage::CHAT_AUTO_WATSON_REPLY) {
                                $chatbotReply->chat_id = $message->id;
                                $chatbotReply->answer = $reply->suggested_reply;
                                $chatbotReply->reply = '{"output":{"database":[{"response_type":"text","text":"'.$reply->suggested_reply.'"}]}}';
                                $chatbotReply->reply_from = 'erp';
                                $chatbotReply->save();
                                if (isset($params['chat_message_log_id'])) {
                                    $data = [
                                        'chatbot_message_log_id' => $params['chat_message_log_id'],
                                        'request' => '',
                                        'response' => 'CHAT_AUTO_WATSON_REPLY: '.$chatbotReply->reply,
                                        'status' => 'success',
                                    ];
                                    $chat_message_log = ChatbotMessageLogResponse::StoreLogResponse($data);
                                }

                                WatsonChatJourney::updateOrCreate(['chat_message_id' => $messageModel->id], ['reply_searched_in_watson' => 1]);
                                WatsonChatJourney::updateOrCreate(['chat_message_id' => $messageModel->id], ['reply' => $chatbotReply->reply]);
                            } else {
                                WatsonChatJourney::updateOrCreate(['chat_message_id' => $messageModel->id], ['reply_found_in_database' => 1]);

                                $log_comment = $log_comment.' Message status is not equal to chat auto watson reply ';
                            }

                            // Send message if all required data is set
                            if ($temp_params['message'] || $temp_params['media_url']) {
                                if ($status == 2) {
                                    $sendResult = app(WhatsAppController::class)->sendWithThirdApi($customer->phone, isset($instanceNumber) ? $instanceNumber : null, $temp_params['message'], $temp_params['media_url']);

                                    WatsonChatJourney::updateOrCreate(['chat_message_id' => $messageModel->id], ['response_sent_to_cusomer' => 1]);

                                    if ($sendResult) {
                                        $message->unique_id = $sendResult['id'] ?? '';
                                        $message->save();
                                    }
                                    if (isset($params['chat_message_log_id'])) {
                                        $data = [
                                            'chatbot_message_log_id' => $params['chat_message_log_id'],
                                            'request' => '',
                                            'response' => $sendResult,
                                            'status' => 'success',
                                        ];
                                        $chat_message_log = ChatbotMessageLogResponse::StoreLogResponse($data);
                                    } else {
                                        $log_comment = $log_comment.' Chat message log ID not found ';
                                    }
                                }
                                $isReplied = 1;
                                break;
                            } else {
                                $log_comment = $log_comment.'Message and media URL not found ';
                            }
                        } else {
                            $log_comment = $log_comment.' Keyword is not equal to message ';
                        }
                    } else {
                        $log_comment = $log_comment.' Message is empty or customer not found ';
                    }
                }
            } else {
                $log_comment = $log_comment.' Chat message not created ';
            }

            // assigned the first storewebsite to default erp customer
            $customer->store_website_id = ($customer->store_website_id > 0) ? $customer->store_website_id : 1;

            if (isset($params['chat_message_log_id'])) {
                ChatbotMessageLogResponse::StoreLogResponse([
                    'chatbot_message_log_id' => $params['chat_message_log_id'],
                    'request' => '',
                    'response' => 'Auto replied match found : '.$isReplied.' and  customer store website id '.$customer->store_website_id,
                    'status' => 'success',
                ]);
            } else {
                $log_comment = $log_comment.' Chat message log ID not found ';
            }

            if (! $isReplied && $customer->store_website_id) {
                if (isset($params['chat_message_log_id'])) {
                    ChatbotMessageLogResponse::StoreLogResponse([
                        'chatbot_message_log_id' => $params['chat_message_log_id'],
                        'request' => '',
                        'response' => 'Watson manager send function started',
                        'status' => 'success',
                    ]);
                } else {
                    $log_comment = $log_comment.' Chat message log ID not found ';
                }

                $watsonmanager_response = WatsonManager::sendMessage($customer, $message, false, null, $messageModel, $userType, isset($params['chat_message_log_id']) ? $params['chat_message_log_id'] : null);

                if (isset($params['chat_message_log_id'])) {
                    ChatbotMessageLogResponse::StoreLogResponse([
                        'chatbot_message_log_id' => $params['chat_message_log_id'],
                        'request' => '',
                        'response' => 'Watson manager send function finished',
                        'status' => 'success',
                    ]);
                } else {
                    $log_comment = $log_comment.' Chat message log ID not found ';
                }
            } else {
                $log_comment = $log_comment.' Store website not found ';

                if (isset($params['chat_message_log_id'])) {
                    ChatbotMessageLogResponse::StoreLogResponse([
                        'chatbot_message_log_id' => $params['chat_message_log_id'],
                        'request' => '',
                        'response' => 'Watson manager send function end replied found',
                        'status' => 'success',
                    ]);
                } else {
                    $log_comment = $log_comment.' Chat message log ID not found ';
                }
            }
        }

        //START - Purpose : Log Comment ,Add Data - DEVTASK-4233
        $log_comment = $log_comment.' . ';
        $temp_log_params['comment'] = $log_comment;

        if (! empty($temp_log_params['keyword_match']) && $temp_log_params['keyword_match'] != '') {
            $add_keyword = KeywordAutoGenratedMessageLog::create($temp_log_params);
        }
        //END - DEVTASK-4233
    }

    public static function sendEmailOrWebhookNotification($toUsers, $message)
    {
        try {
            $toUsers = array_unique($toUsers);

            foreach ($toUsers as $user_id) {
                $user = User::with('webhookNotification')->find($user_id);

                if (! $user) {
                    continue;
                }

                $webhookNotification = $user->webhookNotification;

                $webhookClient = new GuzzleClient;

                $webhookClient->{$webhookNotification->method}($webhookNotification->url, [
                    'body' => str_replace('[MESSAGE]', $message, $webhookNotification->payload),
                    'connect_timeout' => 3,
                    'headers' => ['Content-Type' => $webhookNotification->content_type],
                ]);
            }
        } catch (Exception $e) {
            Log::channel('errorlog')->debug($e->getMessage().' | Line no: '.$e->getLine().' | '.$e->getFile());
        }
    }

    public static function getMessageByCharityId($charityId)
    {
        return ChatMessage::where('charity_id', $charityId)->latest()->first();
    }

    public static function getLastMessageByUserFeedbakAndCategoryId($userFeedbakID, $categoryID)
    {
        return ChatMessage::select('message')->where('user_feedback_id', $userFeedbakID)->where('user_feedback_category_id', $categoryID)->orderBy('id', 'DESC')->first();
    }
}
