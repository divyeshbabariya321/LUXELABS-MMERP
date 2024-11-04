<?php

namespace App\Http\Controllers;

use App\ChatbotReply;
use App\ChatMessage;
use App\CouponCodeRuleLog;
use App\CouponCodeRules;
use App\CreditEmailLog;
use App\CreditHistory;
use App\CreditLog;
use App\Currency;
use App\Customer;
use App\CustomerLiveChat;
use App\Email;
use App\EmailAddress;
use App\ErpLeads;
use App\Helpers\OrderHelper;
use App\Helpers\TranslationHelper;
use App\Http\Requests\AssignTicketLiveChatRequest;
use App\Http\Requests\SendEmailLiveChatRequest;
use App\Http\Requests\TicketStatusLiveChatRequest;
use App\Jobs\SendEmail;
use App\Library\Watson\Model as WatsonManager;
use App\LiveChatEventLog;
use App\LivechatincSetting;
use App\LiveChatLog;
use App\LiveChatUser;
use App\LogRequest;
use App\Mail\AddCoupon;
use App\Mails\Manual\PurchaseEmail;
use App\Mails\Manual\SendIssueCredit;
use App\Models\DataTableColumn;
use App\Models\EmailStatus;
use App\Order;
use App\Product;
use App\Reply;
use App\ReplyCategory;
use App\Services\CommonGoogleTranslateService;
use App\Setting;
use App\Sop;
use App\StoreWebsite;
use App\Supplier;
use App\Tickets;
use App\TicketStatuses;
use App\TodoCategory;
use App\TodoStatus;
use App\User;
use App\Vendor;
use App\WatsonChatJourney;
use App\WebsiteStore;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;

class LiveChatController extends Controller
{
    //Webhook
    public function incoming(Request $request)
    {
        Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- incoming >>');

        Log::channel('chatapi')->info($request->getContent());

        $receivedJson = json_decode($request->getContent());

        $eventType = '';
        $threadId = '';
        $customerId = 0;
        $websiteId = 0;
        if (isset($receivedJson->payload->chat)) {
            $chat = $receivedJson->payload->chat;
            $threadId = $chat->id;
        } elseif (isset($receivedJson->payload->chat_id)) {
            $threadId = $receivedJson->payload->chat_id;
        }
        WatsonChatJourney::updateOrCreate(['chat_id' => $threadId], ['chat_entered' => 1]);
        if (isset($receivedJson->payload->chat->thread->properties->routing->start_url)) {
            $websiteURL = self::getDomain($receivedJson->payload->chat->thread->properties->routing->start_url);
            $website = StoreWebsite::where('website', 'like', '%'.$websiteURL.'%')->first();
            if ($website) {
                $websiteId = $website->id;
            }
        } else {
            $websiteId = LiveChatEventLog::where('thread', $threadId)->whereNotNull('store_website_id')->where('store_website_id', '<>', 0)->pluck('store_website_id')->first();
        }
        LiveChatEventLog::create(['customer_id' => 0, 'thread' => $threadId, 'event_type' => '', 'store_website_id' => $websiteId, 'log' => json_encode($receivedJson)]);

        if (isset($receivedJson->event_type)) {
            $eventType = $receivedJson->event_type;

            Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'--event_type >>');
            //When customer Starts chat
            if ($receivedJson->event_type == 'chat_started') {
                Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'--chat_started >>');
                ///Getting the chat
                $chat = $receivedJson->chat;

                //Getting Agent
                $agent = $chat->agents;
                // name": "SoloLuxury"
                // +"login": "yogeshmordani@icloud.com"
                $chat_survey = $receivedJson->pre_chat_survey;
                $detials = [];
                foreach ($chat_survey as $survey) {
                    $label = strtolower($survey->label);

                    if (strpos($label, 'name') !== false) {
                        array_push($detials, $survey->answer);
                    }
                    if (strpos($label, 'e-mail') !== false) {
                        array_push($detials, $survey->answer);
                    }
                    if (strpos($label, 'phone') !== false) {
                        array_push($detials, $survey->answer);
                    }
                }

                $name = $detials[0];
                $email = $detials[1];
                //Check if customer exist

                $customer = Customer::where('email', $email)->first();

                //Save Customer
                if ($customer == null && $customer == '') {
                    Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- save customer >>'.$name);

                    $customer = new Customer;
                    $customer->name = $name;
                    $customer->email = $email;
                    $customer->phone = null;
                    $customer->language = 'en';
                    $customer->save();
                }
                $customerId = $customer->id;
                LiveChatEventLog::create(['customer_id' => $customerId, 'thread' => $threadId, 'store_website_id' => $websiteId, 'event_type' => $eventType, 'log' => 'entered in chat started condition']);
                LiveChatEventLog::create(['customer_id' => $customerId, 'thread' => $threadId, 'store_website_id' => $websiteId, 'event_type' => $eventType, 'log' => 'customer details fetched']);
            }
        }

        if (isset($receivedJson->action)) {
            Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- action >>');
            //Incomg Event
            if ($receivedJson->action == 'incoming_event') {
                Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- incoming_event >>');

                //Chat Details
                $chatDetails = $receivedJson->payload;
                //Chat Details
                $chatId = $chatDetails->chat_id;

                //Check if customer which has this id
                $customerLiveChat = CustomerLiveChat::where('thread', $chatId)->first();

                //update to not seen
                if ($customerLiveChat != null) {
                    $customerLiveChat->seen = 0;
                    $customerLiveChat->status = 1;
                    $customerLiveChat->update();
                }
                if ($chatDetails->event->type == 'message') {
                    Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- message >>');

                    $message = $chatDetails->event->text;
                    $author_id = $chatDetails->event->author_id;

                    // Finding Agent
                    $agent = User::where('email', $author_id)->first();

                    if ($agent != '' && $agent != null) {
                        $userID = $agent->id;
                    } else {
                        $userID = null;
                    }

                    $customerDetails = Customer::find($customerLiveChat->customer_id);
                    $language = $customerDetails->language;
                    LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'thread' => $threadId, 'event_type' => '', 'store_website_id' => $websiteId, 'log' => 'customer language  '.$language]);

                    LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'thread' => $threadId, 'event_type' => 'incoming_chat', 'store_website_id' => $websiteId, 'log' => 'google key used  '.config('env.GOOGLE_TRANSLATE_API_KEY')]);
                    $result = (new CommonGoogleTranslateService)->detectLanguage($message);
                    if (isset($result['languageCode'])) {
                        $customerDetails->language = $result['languageCode'];

                        $language = $result['languageCode'];
                        LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'thread' => $threadId, 'event_type' => 'incoming_chat', 'store_website_id' => $websiteId, 'log' => ' language detected '.$language]);
                    } elseif (isset($result->message)) {
                        LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'thread' => $threadId, 'event_type' => 'incoming_chat', 'store_website_id' => $websiteId, 'log' => 'Googlr translation'.$result->message]);
                    }

                    $result = (new CommonGoogleTranslateService)->translate($language, $message);
                    if ($result != null) {
                        $message = $result;
                    } else {
                        $result = 'Could not convert Message';
                    }
                    LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'thread' => $threadId, 'event_type' => '', 'store_website_id' => $websiteId, 'log' => ' translated message '.$result]);

                    if ($author_id == 'buying@amourint.com') {
                        $messageStatus = 2;
                    } else {
                        $messageStatus = 9;
                    }

                    $message_application_id = 2;

                    $params = [
                        'unique_id' => $chatDetails->chat_id,
                        'message' => $message,
                        'customer_id' => $customerLiveChat->customer_id,
                        'approved' => 1,
                        'status' => $messageStatus,
                        'is_delivered' => 1,
                        'user_id' => $userID,
                        'message_application_id' => $message_application_id,
                    ];
                    LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'store_website_id' => $websiteId, 'thread' => $chatId, 'event_type' => 'incoming_event', 'log' => 'Customer details fetched']);
                    LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'store_website_id' => $websiteId, 'thread' => $chatId, 'event_type' => 'incoming_event', 'log' => 'Entered in incoming_event message condition']);

                    // Create chat message
                    $chatMessage = ChatMessage::create($params);
                    LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'store_website_id' => $websiteId, 'thread' => $chatId, 'event_type' => 'incoming_event', 'log' => 'Message saved in chat messages.']);

                    //STRAT - Purpose : Add record in chatbotreplay - DEVTASK-18280
                    if ($messageStatus != 2) {
                        ChatbotReply::create([
                            'question' => $message,
                            'reply' => json_encode([
                                'context' => 'chatbot',
                                'issue_id' => $chatDetails->chat_id,
                                'from' => 'chatbot',
                            ]),
                            'replied_chat_id' => $chatMessage->id,
                            'reply_from' => 'chatbot',
                        ]);
                        WatsonChatJourney::updateOrCreate(['chat_id' => $threadId], ['message_received' => $message]);
                        LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'store_website_id' => $websiteId, 'thread' => $chatId, 'event_type' => 'incoming_event', 'log' => $message.' saved in chatbot reply .']);
                    }
                    //END - DEVTASK-18280

                    // if customer found then send reply for it
                    if (! empty($customerDetails) && $message != '') {
                        LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'store_website_id' => $websiteId, 'thread' => $chatId, 'event_type' => 'incoming_event', 'log' => 'Message sent to watson '.$message]);
                        WatsonManager::sendMessage($customerDetails, $message, '', $message_application_id);
                    }
                }

                if ($chatDetails->event->type == 'file') {
                    Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- file >>');

                    $author_id = $chatDetails->event->author_id;

                    // Finding Agent
                    $agent = User::where('email', $author_id)->first();

                    if ($agent != null) {
                        $userID = $agent->id;
                    } else {
                        $userID = null;
                    }

                    if ($author_id == 'buying@amourint.com') {
                        $messageStatus = 2;
                    } else {
                        $messageStatus = 9;
                    }

                    //creating message
                    $params = [
                        'unique_id' => $chatDetails->chat_id,
                        'customer_id' => $customerLiveChat->customer_id,
                        'approved' => 1,
                        'status' => $messageStatus,
                        'is_delivered' => 1,
                        'user_id' => $userID,
                        'message_application_id' => 2,
                    ];
                    // Create chat message
                    $chatMessage = ChatMessage::create($params);

                    $numberPath = date('d');
                    $url = $chatDetails->event->url;
                    try {
                        $jpg = \Image::make($url)->encode('jpg');
                        $filename = $chatDetails->event->name;
                        $media = MediaUploader::fromString($jpg)->toDirectory('/chat-messages/'.$numberPath)->useFilename($filename)->upload();
                        $chatMessage->attachMedia($media, config('constants.media_tags'));
                    } catch (Exception $e) {
                        $file = @file_get_contents($url);
                        if (! empty($file)) {
                            $filename = $chatDetails->event->name;
                            $media = MediaUploader::fromString($file)->toDirectory('/chat-messages/'.$numberPath)->useFilename($filename)->upload();
                            $chatMessage->attachMedia($media, config('constants.media_tags'));
                        }
                    }
                }

                if ($chatDetails->event->type == 'system_message') {
                    $customerLiveChat = CustomerLiveChat::where('thread', $chatId)->first();
                    if ($customerLiveChat != null) {
                        $customerLiveChat->status = 0;
                        $customerLiveChat->seen = 1;
                        $customerLiveChat->update();
                    }
                }

                // Add to chat_messages if we have a customer
            }

            if ($receivedJson->action == 'incoming_chat') {
                Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- incoming_chat >>');

                $chat = $receivedJson->payload->chat;
                $chatId = $chat->id;

                if (isset($chat->thread->events[0]->fields[1]->value)) {
                    $userEmail = $chat->thread->events[0]->fields[1]->value;
                } elseif (isset($chat->thread->events[2]->fields[1]->value)) {
                    $userEmail = $chat->thread->events[2]->fields[1]->value;
                } else {
                    $userEmail = null;
                }
                $userName = $chat->users[0]->name;

                $customer_language = 'en';
                $websiteId = null;
                try {
                    $websiteURL = self::getDomain($chat->thread->properties->routing->start_url);
                    $website = StoreWebsite::where('website', 'like', '%'.$websiteURL.'%')->first();
                    if ($website) {
                        $websiteId = $website->id;
                    }
                } catch (Exception $e) {
                    $websiteURL = '';
                }
                $customer = Customer::where('email', $userEmail);
                if ($websiteId > 0) {
                    $customer = $customer->where('store_website_id', $websiteId);
                }
                $customer = $customer->first();

                if ($customer != null) {
                    Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- Customer Null');

                    //Find if its has ID
                    $chatID = CustomerLiveChat::where('customer_id', $customer->id)->where('thread', $chatId)->first();
                    if ($chatID == null) {
                        Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- chatID Null');

                        //check if only thread exist and make it null
                        $onlyThreadCheck = CustomerLiveChat::where('thread', $chatId)->first();
                        if ($onlyThreadCheck) {
                            $onlyThreadCheck->thread = null;
                            $onlyThreadCheck->seen = 1;
                            $onlyThreadCheck->save();
                        }

                        Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- Add CustomerLiveChat  111 ::::'.$chatId);
                        $customerChatId = new CustomerLiveChat;
                        $customerChatId->customer_id = $customer->id;
                        $customerChatId->thread = $chatId;
                        $customerChatId->status = 1;
                        $customerChatId->seen = 0;
                        $customerChatId->website = $websiteURL;
                        $customerChatId->save();
                    } else {
                        Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- chatID Not Null');
                        Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- Add CustomerLiveChat 222 ::::'.$chatId);
                        $chatID->customer_id = $customer->id;
                        $chatID->thread = $chatId;
                        $chatID->status = 1;
                        $chatID->website = $websiteURL;
                        $chatID->seen = 0;
                        $chatID->update();
                    }
                } else {
                    Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- Customer Not Null');

                    //check if only thread exist and make it null
                    $onlyThreadCheck = CustomerLiveChat::where('thread', $chatId)->first();
                    if ($onlyThreadCheck) {
                        $onlyThreadCheck->thread = null;
                        $onlyThreadCheck->seen = 1;
                        $onlyThreadCheck->save();
                    }

                    Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- Add Customer ::::'.$userName);
                    $customer = new Customer;
                    $customer->name = $userName;
                    $customer->email = $userEmail;
                    $customer->language = $customer_language;
                    $customer->phone = null;
                    $customer->store_website_id = $websiteId;
                    $customer->language = 'en';
                    $customer->save();

                    //Save Customer with Chat ID
                    Log::channel('chatapi')->debug(': ChatApi'."\nMessage :".'-- Add CustomerLiveChat 3333::::'.$chatId);
                    $customerChatId = new CustomerLiveChat;
                    $customerChatId->customer_id = $customer->id;
                    $customerChatId->thread = $chatId;
                    $customerChatId->status = 1;
                    $customerChatId->seen = 0;
                    $customerChatId->website = $websiteURL;
                    $customerChatId->save();
                }
                try {
                    $text = $chat->thread->events[1]->text;
                    LiveChatEventLog::create(['customer_id' => $customer->id, 'store_website_id' => $websiteId, 'thread' => $chatId, 'event_type' => 'incoming_chat', 'log' => 'text found '.$text]);
                    $replyFound = ChatbotReply::where('answer', $text)->first();
                    if ($replyFound == null) {
                        WatsonChatJourney::updateOrCreate(['chat_id' => $threadId], ['reply_searched_in_watson' => 1]);
                    } else {
                        WatsonChatJourney::updateOrCreate(['chat_id' => $threadId], ['reply_found_in_database' => 1]);
                    }

                    WatsonChatJourney::updateOrCreate(['chat_id' => $threadId], ['reply' => $text]);
                    WatsonChatJourney::updateOrCreate(['chat_id' => $threadId], ['response_sent_to_cusomer' => 1]);
                } catch (Exception $e) {
                    LiveChatEventLog::create(['customer_id' => $customer->id, 'store_website_id' => $websiteId, 'thread' => $chatId, 'event_type' => 'incoming_chat', 'log' => 'incoming chat error '.$e.' <br>data '.json_encode($chat->thread->events[1])]);
                    $text = 'Error';
                }
            }

            if ($receivedJson->action == 'thread_closed') {
                $chatId = $receivedJson->payload->chat_id;

                $customerLiveChat = CustomerLiveChat::where('thread', $chatId)->first();

                if ($customerLiveChat != null) {
                    $customerLiveChat->thread = null;
                    $customerLiveChat->status = 0;
                    $customerLiveChat->seen = 1;
                    $customerLiveChat->update();
                }
                LiveChatEventLog::create(['customer_id' => $customerLiveChat->customer_id, 'store_website_id' => $websiteId, 'thread' => $chatId, 'event_type' => 'thread_closed', 'log' => 'Chat thread closed ']);
            }
        }
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $chatId = $request->id;
        $message = $request->message;
        $eventType = 'send_message';
        $customerDetails = Customer::find($chatId);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $language = $customerDetails->language;
        if ($language != null) {
            $message = (new CommonGoogleTranslateService)->translate($language, $message);
        }
        if (isset($request->messageId)) {
            $chatMessages = ChatMessage::where('id', $request->messageId)->first();
            if ($chatMessages != null) {
                $chatMessages->approved = 1;
                $chatMessages->save();
            }
        }

        //Get Thread ID From Customer Live Chat
        $customer = CustomerLiveChat::where('customer_id', $chatId)->where('thread', '!=', '')->latest()->first();

        if ($customer != null) {
            $thread = $customer->thread;
            $websiteId = LiveChatEventLog::where('thread', $thread)->whereNotNull('store_website_id')->pluck('store_website_id')->first();
            LiveChatEventLog::create(['customer_id' => $customer->id, 'thread' => $thread, 'store_website_id' => $websiteId, 'event_type' => $eventType, 'log' => 'Customer live chat found']);
            if ($language != null) {
                LiveChatEventLog::create(['customer_id' => $customer->id, 'thread' => $thread, 'store_website_id' => $websiteId, 'event_type' => $eventType, 'log' => 'Customer language '.$language]);
                LiveChatEventLog::create(['customer_id' => $customer->id, 'thread' => $thread, 'store_website_id' => $websiteId, 'event_type' => $eventType, 'log' => 'message converted from '.$request->message.' to '.$message]);
            }
        } else {
            return response()->json([
                'status' => 'errors',
            ]);
        }

        $post = ['chat_id' => $thread, 'event' => ['type' => 'message', 'text' => $message, 'recipients' => 'all']];
        $post = json_encode($post);
        $url = 'https://api.livechatinc.com/v3.1/agent/action/send_event';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "$post",
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.Cache::get('key').'',
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        LogRequest::log($startTime, $url, 'GET', json_encode($post), json_decode($response), $httpcode, WhatsAppController::class, 'downloadFromURL');

        LiveChatEventLog::create(['customer_id' => $customer->id, 'thread' => $thread, 'store_website_id' => $websiteId, 'event_type' => $eventType, 'log' => 'Token used '.Cache::get('key')]);
        LiveChatEventLog::create(['customer_id' => $customer->id, 'thread' => $thread, 'store_website_id' => $websiteId, 'event_type' => $eventType, 'log' => $response]);

        if ($err) {
            LiveChatEventLog::create(['customer_id' => $customer->id, 'thread' => $thread, 'store_website_id' => $websiteId, 'event_type' => $eventType, 'log' => $err]);

            return response()->json([
                'status' => 'errors',
            ]);
        } else {
            $response = json_decode($response);

            if (isset($response->error)) {
                LiveChatEventLog::create(['customer_id' => $customer->id, 'store_website_id' => $websiteId, 'event_type' => $eventType, 'thread' => $thread, 'log' => $response->error->message]);

                return response()->json([
                    'status' => 'errors '.@$response->error->message,
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                ]);
            }
        }
    }

    public function setting(): View
    {
        $liveChatUsers = LiveChatUser::all();
        $setting = LivechatincSetting::first();
        $users = User::where('is_active', 1)->get();

        return view('livechat.setting', compact('users', 'liveChatUsers', 'setting'));
    }

    public function remove(Request $request): JsonResponse
    {
        $users = LiveChatUser::findorfail($request->id);
        $users->delete();

        return response()->json(['success' => 'success'], 200);
    }

    public function save(Request $request): RedirectResponse
    {
        if ($request->username != '' || $request->key != '') {
            $checkIfExist = LivechatincSetting::all();
            if (count($checkIfExist) == 0) {
                $setting = new LivechatincSetting;
                $setting->username = $request->username;
                $setting->key = $request->key;
                $setting->save();
            } else {
                $setting = LivechatincSetting::first();
                $setting->username = $request->username;
                $setting->key = $request->key;
                $setting->update();
            }
        }

        if ($request->users != null && $request->users != '') {
            $users = $request->users;
            foreach ($users as $user) {
                $userCheck = LiveChatUser::where('user_id', $user)->first();
                if ($userCheck != '' && $userCheck != null) {
                    continue;
                }
                $userss = new LiveChatUser;
                $userss->user_id = $user;
                $userss->save();
            }
        }

        return redirect()->back()->withSuccess(['msg', 'Saved']);
    }

    public function uploadFileToLiveChat($image)
    {
        //Save file to path
        //send path to Live chat
        $curl = curl_init();
        $url = 'https://api.livechatinc.com/v3.2/agent/action/upload_file';
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => ['file' => new CURLFILE('/Users/satyamtripathi/PhpstormProjects/untitled/images/1592232591.png')],
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.Cache::get('key').'',
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        curl_close($curl);
        LogRequest::log($startTime, $url, 'POST', json_encode(['file' => new CURLFILE('/Users/satyamtripathi/PhpstormProjects/untitled/images/1592232591.png')]),
            json_decode($response),
            $httpcode, LiveChatController::class, 'curlCall');
        echo $response;
    }

    public function getorderdetails(Request $request)
    {
        $customer_id = $request->customer_id;

        $customer = $this->findCustomerById($customer_id);

        if ($customer) {
            $orders = (new Order)->newQuery()->with('customer')->leftJoin('store_website_orders as swo', 'swo.order_id', 'orders.id')
                ->leftJoin('order_products as op', 'op.order_id', 'orders.id')
                ->leftJoin('products as p', 'p.id', 'op.product_id')
                ->leftJoin('brands as b', 'b.id', 'p.brand')->groupBy('orders.id')
                ->where('customer_id', $customer->id)
                ->select(['orders.*', DB::raw('group_concat(b.name) as brand_name_list'), 'swo.website_id'])->orderByDesc('created_at')->get();
            [$leads_total, $leads] = $this->getLeadsInformation($customer->id);
            $exchanges_return = $customer->return_exchanges;
            if ($orders->count()) {
                foreach ($orders as &$value) {
                    $value->storeWebsite = $value->storeWebsiteOrder ? ($value->storeWebsiteOrder->storeWebsite ?? 'N/A') : 'N/A';
                    $value->order_date = Carbon::parse($value->order_date)->format('d-m-y');
                    $totalBrands = explode(',', $value->brand_name_list);
                    $value->brand_name_list = (count($totalBrands) > 1) ? 'Multi' : $value->brand_name_list;
                    $value->status = OrderHelper::getStatusNameById($value->order_status_id);
                }
            }

            return [
                true,
                [
                    'orders_total' => $orders->count(),
                    'leads_total' => $leads_total,
                    'exchanges_return_total' => $exchanges_return->count(),
                    'exchanges_return' => $exchanges_return,
                    'leads' => $leads,
                    'orders' => $orders,
                    'customer' => $customer,
                ],
            ];
        }

        return [false, false];
    }

    protected function findCustomerById($customer_id)
    {
        return Customer::where('id', '=', $customer_id)->first();
    }

    private function getLeadsInformation($id)
    {
        $source = ErpLeads::leftJoin('products', 'products.id', '=', 'erp_leads.product_id')
            ->leftJoin('customers as c', 'c.id', 'erp_leads.customer_id')
            ->leftJoin('erp_lead_status as els', 'els.id', 'erp_leads.lead_status_id')
            ->leftJoin('categories as cat', 'cat.id', 'erp_leads.category_id')
            ->leftJoin('brands as br', 'br.id', 'erp_leads.brand_id')
            ->where('erp_leads.customer_id', $id)
            ->orderByDesc('erp_leads.id')
            ->select(['erp_leads.*', 'products.name as product_name', 'cat.title as cat_title', 'br.name as brand_name', 'els.name as status_name', 'c.name as customer_name', 'c.id as customer_id']);

        $total = $source->count();
        $source = $source->get();

        foreach ($source as $key => $value) {
            $source[$key]->media_url = null;
            $media = $value->getMedia(config('constants.media_tags'))->first();
            if ($media) {
                $source[$key]->media_url = getMediaUrl($media);
            }

            if (empty($source[$key]->media_url) && $value->product_id) {
                $product = Product::find($value->product_id);
                if ($product) {
                    $media = $product->getMedia(config('constants.media_tags'))->first();
                    if ($media) {
                        $source[$key]->media_url = getMediaUrl($media);
                    }
                }
            }
        }

        return [$total, $source];
    }

    public function getChats(Request $request): JsonResponse
    {
        $chatId = $request->id;
        $messagess = [];

        //put session
        session()->put('chat_customer_id', $chatId);

        //update chat has been seen
        $customer = CustomerLiveChat::where('customer_id', $chatId)->where('thread', '!=', '')->latest()->first();

        if ($customer != null) {
            $customer->seen = 1;
            $customer->update();
        }

        $threadId = $customer->thread;

        $messages = ChatMessage::where('customer_id', $chatId)->where('message_application_id', 2)->get();

        //getting customer name from chat
        $customer = Customer::findorfail($chatId);
        $name = $customer->name;
        $store_website_id = $customer->store_website_id;
        $customerInfo = $this->getLiveChatIncCustomer($customer->email, 'raw');
        if (! $customerInfo) {
            $customerInfo = '';
        }

        $customerInital = substr($name, 0, 1);
        if (count($messages) != 0) {
            foreach ($messages as $message) {
                $agent = Customer::where('id', $message->customer_id)->first();
                $agentInital = substr($agent->name, 0, 1);

                if ($message->status == 2) {
                    $type = 'end';
                } else {
                    $type = 'start';
                }

                if ($message->hasMedia(config('constants.media_tags'))) {
                    foreach ($message->getMedia(config('constants.media_tags')) as $image) {
                        if (! $message->approved) {
                            $vals = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer"><span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span><div class="d-flex mb-4"><div class="d-flex  mb-4"><input type="hidden" id="message-id" name="message-id" value="'.$chatId.'"><input type="hidden" id="message-value" name="message-value" value="'.$message->message.'"><button id="'.$message->id.'" class="btn btn-secondary quick_approve_add_live">Approve Message</button></div><div class="msg_cotainer_send"><img src="'.getMediaUrl($image).'" class="rounded-circle-livechat user_img_msg"></div></div>';
                        } else {
                            $vals = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer"><span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div><div class="msg_cotainer_send"><img src="'.getMediaUrl($image).'" class="rounded-circle-livechat user_img_msg"></div></div>';
                        }
                        $messagess[] = $vals;
                    }
                } else {
                    if ($message->user_id != 0) {
                        // Finding Agent
                        $agent = User::select('name')->where('id', $message->user_id)->first();
                        $agentInital = substr($agent->name, 0, 1);
                        if (! $message->approved) {
                            $vals = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer"> '.$message->message.'<br><span class="msg_time"> '.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().' </span><div class="d-flex  mb-4"><input type="hidden" id="message-id" name="message-id" value="'.$chatId.'"><input type="hidden" id="message-value" name="message-value" value="'.$message->message.'"><button id="'.$message->id.'" class="btn btn-secondary quick_approve_add_live">Approve Message</button></div></div></div>';
                        } else {
                            $vals = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer"> '.$message->message.'<br><span class="msg_time"> '.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().' </span> </div></div>';
                        }
                        $messagess[] = $vals;
                    } else {
                        if (! $message->approved) {
                            $vals = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$customerInital.'</div><div class="msg_cotainer">'.$message->message.'<br><span class="msg_time"> '.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().' </span><div class="d-flex  mb-4"><input type="hidden" id="message-id" name="message-id" value="'.$chatId.'"><input type="hidden" id="message-value" name="message-value" value="'.$message->message.'"><button id="'.$message->id.'" class="btn btn-secondary quick_approve_add_live">Approve Message</button></div></div></div>';
                        } else {
                            $vals = '<div  data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' sss mb-4"><div class="rounded-circle user_inital">'.$customerInital.'</div><div class="msg_cotainer">'.$message->message.'<br><span class="msg_time"> '.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().' </span></div></div>';
                        }
                        $messagess[] = $vals;
                    }
                }
            }
        }

        if (! isset($messagess)) {
            //
        }

        $count = CustomerLiveChat::where('seen', 0)->count();

        return response()->json([
            'status' => 'success',
            'data' => ['id' => $chatId, 'count' => $count, 'message' => $messagess, 'name' => $name, 'customerInfo' => $customerInfo, 'threadId' => $threadId, 'customerInital' => $customerInital, 'store_website_id' => $store_website_id],
        ]);
    }

    public function getLastChats(Request $request): JsonResponse
    {
        $chatId = $request->id;
        $messages = ChatMessage::where('customer_id', $chatId)->where('message_application_id', 2)->orderByDesc('id')->first();

        return response()->json([
            'status' => 'success',
            'data' => $messages,
        ]);
    }

    public function getChatMessagesWithoutRefresh(): JsonResponse
    {
        $messagess = [];
        if (session()->has('chat_customer_id')) {
            $lastMessageId = request('last_msg_id');
            $chatId = session()->get('chat_customer_id');
            $messages = ChatMessage::where('customer_id', $chatId)->where('message_application_id', 2);
            if ($lastMessageId != null) {
                $messages = $messages->where('id', '>', $lastMessageId);
            }
            $messages = $messages->get();

            //getting customer name from chat
            $customer = Customer::findorfail($chatId);
            $name = $customer->name;
            $customerInital = substr($name, 0, 1);
            if (count($messages) == 0) {
                if ($lastMessageId == null) {
                    //
                }
            } else {
                foreach ($messages as $message) {
                    if ($message->user_id != 0) {
                        // Finding Agent
                        $agent = Customer::where('id', $message->customer_id)->first();
                        $agentInital = substr($agent->name, 0, 1);

                        if ($message->hasMedia(config('constants.media_tags'))) {
                            foreach ($message->getMedia(config('constants.media_tags')) as $image) {
                                if ($message->status == 2) {
                                    $type = 'end';
                                } else {
                                    $type = 'start';
                                }

                                if (! $message->approved) {
                                    $vals = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer"><span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span><div class="d-flex mb-4"><div class="d-flex  mb-4"><input type="hidden" id="message-id" name="message-id" value="'.$chatId.'"><input type="hidden" id="message-value" name="message-value" value="'.$message->message.'"><button id="'.$message->id.'" class="btn btn-secondary quick_approve_add_live">Approve Message</button></div><div class="msg_cotainer_send"><img src="'.getMediaUrl($image).'" class="rounded-circle-livechat user_img_msg"></div></div>';
                                } else {
                                    $vals = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer"><span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div><div class="msg_cotainer_send"><img src="'.getMediaUrl($image).'" class="rounded-circle-livechat user_img_msg"></div></div>';
                                }
                                $messagess[] = $vals;
                            }
                        } else {
                            if ($message->status == 2) {
                                $type = 'end';
                            } else {
                                $type = 'start';
                            }
                            if (! $message->approved) {
                                $vals = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span><div class="d-flex  mb-4"><input type="hidden" id="message-id" name="message-id" value="'.$chatId.'"><input type="hidden" id="message-value" name="message-value" value="'.$message->message.'"><button id="'.$message->id.'" class="btn btn-secondary quick_approve_add_live">Approve Message</button></div></div></div>';
                            } else {
                                $vals = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div></div>';
                            }
                            $messagess[] = $vals;
                        }
                    } else {
                        if ($message->hasMedia(config('constants.media_tags'))) {
                            foreach ($message->getMedia(config('constants.media_tags')) as $image) {
                                if (strpos(getMediaUrl($image), 'jpeg') !== false) {
                                    $attachment = '<a href="" download><img src="'.getMediaUrl($image).'" class="rounded-circle-livechat user_img_msg"></a>';
                                } else {
                                    $attachment = '<a href="" download>'.$image->filename.'</a>';
                                }
                                if ($message->status == 2) {
                                    $type = 'end';
                                } else {
                                    $type = 'start';
                                }
                                if (! $message->approved) {
                                    $messagess[] = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="msg_cotainer"><span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span><div class="d-flex  mb-4"><input type="hidden" id="message-id" name="message-id" value="'.$chatId.'"><input type="hidden" id="message-value" name="message-value" value="'.$message->message.'"><button id="'.$message->id.'" class="btn btn-secondary quick_approve_add_live">Approve Message</button></div></div><div class="msg_cotainer_send">'.$attachment.'</div></div>';
                                } else {

                                    '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4">
                                        <div class="msg_cotainer">
                                            <span class="msg_time">'.$message->created_at->diffForHumans().'</span>
                                        </div>
                                        <div class="msg_cotainer_send">'.$attachment.'</div>
                                    </div>';
                                }
                            }
                        } else {
                            if ($message->status == 2) {
                                $type = 'end';
                            } else {
                                $type = 'start';
                            }
                            if (! $message->approved) {
                                $messagess[] = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle-livechat user_inital">'.$customerInital.'</div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span><input type="hidden" id="message-id" name="message-id" value="'.$chatId.'"><div class="d-flex  mb-4"><input type="hidden" id="message-value" name="message-value" value="'.$message->message.'"><button id="'.$message->id.'" class="btn btn-secondary quick_approve_add_live">Approve Message</button></div></div></div>';
                            } else {
                                $messagess[] = '<div data-chat-id="'.$message->id.'" class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle-livechat user_inital">'.$customerInital.'</div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div></div>';
                            }
                        }
                    }
                }
            }

            $count = CustomerLiveChat::where('seen', 0)->count();

            return response()->json([
                'status' => 'success',
                'data' => ['id' => $chatId, 'count' => $count, 'message' => $messagess, 'name' => $name, 'customerInital' => $customerInital],
            ]);
        } else {
            return response()->json([
                'data' => ['id' => '', 'count' => 0, 'message' => '', 'name' => '', 'customerInital' => ''],
            ]);
        }
    }

    public function getChatLogs($customerId)
    {
        return LiveChatLog::where('customer_id', $customerId)->orderByDesc('id')->get();
    }

    public function getAllChatEventLogs(): View
    {
        $logs = LiveChatEventLog::leftJoin('customers', 'customers.id', 'live_chat_event_logs.customer_id')
            ->leftJoin('store_websites', 'store_websites.id', 'live_chat_event_logs.store_website_id')
            ->orderByDesc('live_chat_event_logs.id')->select('live_chat_event_logs.*', 'customers.name as customer_name', 'store_websites.title as website')->paginate(30);

        return view('livechat.eventLogs', compact('logs'));
    }

    public function getChatEventLogs($customerId)
    {
        return LiveChatEventLog::where('thread', $customerId)->orderByDesc('id')->get();
    }

    public function getLiveChats(Request $request)
    {
        $store_websites = StoreWebsite::all();
        $website_stores = WebsiteStore::with('storeView')->get();
        $chatIds = CustomerLiveChat::query();
        $customers = Customer::all();
        $newMessageCount = CustomerLiveChat::where('seen', 0)->count();
        $all_categories = ReplyCategory::all();

        if ($request->term != '') {
            $q = ! empty($request->term) ? $request->term : '';
            $chatIds->whereHas('customer', function ($query) use ($q) {
                $query->whereIn('name', $q);
            });
        }
        if ($request->website_name != '') {
            $q = ! empty($request->website_name) ? $request->website_name : '';
            $chatIds->whereIn('website', $q);
        }
        if ($request->date != '') {
            $q = $request->date;
            $chatIds->whereDate('created_at', Carbon::parse($q)->format('Y-m-d'));
        }
        if ($request->search_email != '') {
            $q = $request->search_email;
            $chatIds->whereHas('customer', function ($query) use ($q) {
                $query->where('email', 'LIKE', '%'.$q.'%');
            });
        }
        if ($request->phone_no != '') {
            $q = $request->phone_no;
            $chatIds->whereHas('customer', function ($query) use ($q) {
                $query->where('phone', 'LIKE', '%'.$q.'%');
            });
        }
        if ($request->search_keyword != '') {
            $q = $request->search_keyword;
            $chatIds->whereHas('customer', function ($query) use ($q) {
                $query->where('name', 'LIKE', '%'.$q.'%');
            })
                ->orWhere('website', 'LIKE', '%'.$q.'%')
                ->orWhereHas('customer', function ($query) use ($q) {
                    $query->where('phone', 'LIKE', '%'.$q.'%');
                })->orWhereHas('customer', function ($query) use ($q) {
                    $query->where('email', 'LIKE', '%'.$q.'%');
                });
        }

        $chatIds = $chatIds->latest()->orderBy('seen')->orderByDesc('status')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('livechat.partials.chat-list', compact('chatIds', 'store_websites'))->with('i', ($request->input('page', 1) - 1) * 1)->render(),
            ], 200);
        }

        if (session()->has('chat_customer_id')) {
            $chatId = session()->get('chat_customer_id');
            $chat_message = ChatMessage::where('customer_id', $chatId)->where('message_application_id', 2)->orderByDesc('id')->get();
            //getting customer name from chat
            $customer = Customer::findorfail($chatId);
            $name = $customer->name;
            $customerInital = substr($name, 0, 1);
            if (count($chat_message) == 0) {
                //
            } else {
                foreach ($chat_message as $chat) {
                    if ($chat->user_id != 0) {
                        // Finding Agent
                        $agent = User::where('id', $chat->user_id)->first();
                        $agentInital = substr($agent->name, 0, 1);

                        if (! $chat->approved) {
                            $message[] = '<div data-chat-id="'.$chat->id.'" class="d-flex justify-content-end mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer">'.$chat->message.'<span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($chat->created_at))->diffForHumans().'</span><input type="hidden" id="message-id" name="message-id" value="'.$chatId.'"><input type="hidden" id="message-value" name="message-value" value="'.$message->message.'"><div class="d-flex  mb-4"><button id="'.$message->id.'" class="btn btn-secondary quick_approve_add_live">Approve Message</button></div></div></div>';
                        } else {
                            $message[] = '<div data-chat-id="'.$chat->id.'" class="d-flex justify-content-end mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer">'.$chat->message.'<span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($chat->created_at))->diffForHumans().'</span></div></div>';
                        }
                    } else {
                        $message[] = '<div data-chat-id="'.$chat->id.'" class="d-flex justify-content-start mb-4"><div class="rounded-circle user_inital">'.$customerInital.'</div><div class="msg_cotainer">'.$chat->message.'<span class="msg_time">'.Carbon::createFromTimeStamp(strtotime($chat->created_at))->diffForHumans().'</span></div></div>'; //<div class="img_cont_msg"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>
                    }
                }
            }

            return view('livechat.chatMessages', compact('chatIds', 'message', 'name', 'customerInital', 'store_websites', 'website_stores', 'customers', 'newMessageCount', 'all_categories'));
        } else {
            $count = 0;
            $message = '';
            $customerInital = '';
            $name = '';

            return view('livechat.chatMessages', compact('chatIds', 'message', 'name', 'customerInital', 'store_websites', 'website_stores', 'customers', 'newMessageCount', 'all_categories'));
        }
    }

    public function getUserList(): JsonResponse
    {
        $liveChatCustomers = CustomerLiveChat::orderBy('seen')->where('thread', '!=', '')->where('status', 1)->orderByDesc('status')->get();

        foreach ($liveChatCustomers as $liveChatCustomer) {
            $customer = Customer::where('id', $liveChatCustomer->customer_id)->first();
            $customerInital = substr($customer->name, 0, 1);
            if ($liveChatCustomer->status == 0) {
                $customers[] = '<li onclick="getChats('.$customer->id.')" id="user'.$customer->id.'" style="cursor: pointer;">
                                <input type="hidden" id="live_selected_customer_store" value="'.$customer->store_website_id.'" />
                                <div class="d-flex bd-highlight"><div class="img_cont"><span class="rounded-circle user_inital">'.$customerInital.'</span><span class="online_icon offline"></span>
                                </div><div class="user_info"><span>'.$customer->name.'</span><p style="margin-bottom: 0px;">'.$customer->name.' is offline</p><p style="margin-bottom: 0px;">'.$liveChatCustomer->website.'</p></div></div></li><li>'; //<img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img">
            } elseif ($liveChatCustomer->status == 1 && $liveChatCustomer->seen == 0) {
                $customers[] = '<li onclick="getChats('.$customer->id.')" id="user'.$customer->id.'" style="cursor: pointer;">
                                <input type="hidden" id="live_selected_customer_store" value="'.$customer->store_website_id.'" />
                                <div class="d-flex bd-highlight"><div class="img_cont"><span class="rounded-circle user_inital">'.$customerInital.'</span><span class="online_icon"></span>
                                </div><div class="user_info"><span>'.$customer->name.'</span><p style="margin-bottom: 0px;">'.$customer->name.' is online</p><p style="margin-bottom: 0px;">'.$liveChatCustomer->website.'</p></div><span class="new_message_icon"></span></div></li>'; //<img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img">
            } else {
                $customers[] = '<li onclick="getChats('.$customer->id.')" id="user'.$customer->id.'" style="cursor: pointer;">
                                <input type="hidden" id="live_selected_customer_store" value="'.$customer->store_website_id.'" />
                                <div class="d-flex bd-highlight"><div class="img_cont"><span class="rounded-circle user_inital">'.$customerInital.'</span><span class="online_icon"></span>
                                </div><div class="user_info"><span>'.$customer->name.'</span><p style="margin-bottom: 0px;">'.$customer->name.' is online</p><p style="margin-bottom: 0px;">'.$liveChatCustomer->website.'</p></div></div></li>'; //<img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img">
            }
        }
        if (empty($customers)) {
            $customers[] = '<li><div class="d-flex bd-highlight"><div class="img_cont">
                                </div><div class="user_info"><span>No User Found</span><p></p></div></div></li>';
        }
        //Getting chat counts
        $count = CustomerLiveChat::where('seen', 0)->count();

        return response()->json([
            'status' => 'success',
            'data' => ['count' => $count, 'message' => $customers],
        ]);
    }

    public function checkNewChat(): JsonResponse
    {
        $count = CustomerLiveChat::where('seen', 0)->count();

        return response()->json([
            'status' => 'success',
            'data' => ['count' => $count],
        ]);
    }

    /**
     * function to get customer details from livechatinc
     * https://api.livechatinc.com/v3.1/agent/action/get_customers
     *
     * @param customer's email address
     * @param  mixed  $email
     * @param  mixed  $out
     * @return - response livechatinc object of customer information. If error return false
     */
    public function getLiveChatIncCustomer($email = '', $out = 'JSON')
    {
        $threadId = '';
        if ($email == '' && session()->has('chat_customer_id')) {
            $chatId = session()->get('chat_customer_id');
            $messages = ChatMessage::where('customer_id', $chatId)->where('message_application_id', 2)->get();
            //getting customer name from chat
            $customer = Customer::findorfail($chatId);
            $email = $customer->email;

            $liveChatCustomer = CustomerLiveChat::where('customer_id', $chatId)->first();
            $threadId = $liveChatCustomer->thread;
        }

        $returnVal = '';
        if ($email != '') {
            $postURL = 'https://api.livechatinc.com/v3.1/agent/action/get_customers';

            $postData = ['filters' => ['email' => ['values' => [$email]]]];
            $postData = json_encode($postData);

            $returnVal = '';
            $result = self::curlCall($postURL, $postData, 'application/json');
            if ($result['err']) {
                $returnVal = false;
            } else {
                $response = json_decode($result['response']);
                if (isset($response->error)) {
                    $returnVal = false;
                } else {
                    if (isset($response->customers[0])) {
                        $returnVal = $response->customers[0];
                    } else {
                        $returnVal = false;
                    }
                }
            }
        }

        if ($out == 'JSON') {
            return response()->json(['status' => 'success', 'threadId' => $threadId, 'customerInfo' => $returnVal], 200);
        } else {
            return $returnVal;
        }
    }

    /**
     * function to upload file/image to liveshatinc
     * upload file to livechatinc using their agent /action/upload_file api which will respond with livechatinc CDN url for file uploaded
     * https://api.livechatinc.com/v3.1/agent/action/upload_file
     *
     * @param request
     * @return - response livechatinc CDN url for the file. If error return false
     */
    public function uploadFileToLiveChatInc(Request $request)
    {
        //To try with static file from local file, uncomment below
        $uploadedFile = $request->file('file');
        $mimeType = $uploadedFile->getMimeType();
        $filename = $uploadedFile->getClientOriginalName();

        $postURL = 'https://api.livechatinc.com/v3.1/agent/action/upload_file';

        $postData = ['file' => curl_file_create($uploadedFile, $mimeType, $filename)];

        $result = self::curlCall($postURL, $postData, 'multipart/form-data');
        if ($result['err']) {
            return false;
        } else {
            $response = json_decode($result['response']);
            if (isset($response->error)) {
                return false;
            } else {
                return ['CDNPath' => $response->url, 'filename' => $filename];
            }
        }
    }

    public static function useAbsPathUpload($fileURL)
    {
        $filename = basename($fileURL);
        $postData = ['file' => curl_file_create($fileURL, mime_content_type($fileURL), basename($fileURL))];
        $postURL = 'https://api.livechatinc.com/v3.1/agent/action/upload_file';
        $result = self::curlCall($postURL, $postData, 'multipart/form-data');
        if ($result['err']) {
            return false;
        } else {
            $response = json_decode($result['response']);
            if (isset($response->error)) {
                return false;
            } else {
                return ['CDNPath' => $response->url, 'filename' => $filename];
            }
        }
    }

    /**
     * curlCall function to make a curl call
     *
     * @param
     *   URL - url that we need to access and make curl call,
     *   method - curl call method - GET, POST etc
     *   contentType - Content-Type value to set in headers
     *   data - data that has to be sent in curl call. This can be optional if GET
     * @param  mixed  $URL
     * @param  mixed  $data
     * @param  mixed  $contentType
     * @param  mixed  $defaultAuthorization
     * @param  mixed  $method
     * @return - response from curl call, array(response, err)
     */
    public static function curlCall($URL, $data = false, $contentType = false, $defaultAuthorization = true, $method = 'POST')
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();

        $curlData = [
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ];
        $curlData[CURLOPT_CUSTOMREQUEST] = $method;
        if ($contentType) {
            $curlData[CURLOPT_HTTPHEADER] = [];
            if ($defaultAuthorization) {
                array_push($curlData[CURLOPT_HTTPHEADER], 'Authorization: Bearer '.Cache::get('key').'');
            }
            array_push($curlData[CURLOPT_HTTPHEADER], 'Content-Type: '.$contentType);
        }

        if ($data) {
            $curlData[CURLOPT_POSTFIELDS] = $data;
        } else {
            $curlData[CURLOPT_POSTFIELDS] = '{}';
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt_array($curl, $curlData);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        curl_close($curl);
        LogRequest::log($startTime, $URL, 'POST', json_encode($curlData[CURLOPT_POSTFIELDS]), json_decode($response), $httpcode, LiveChatController::class, 'curlCall');

        return ['response' => $response, 'err' => $err];
    }

    /**
     * CDN URL got after uploading file to livechatinc will expire in 24hrs unless its used in sent_event api
     * send the CDN URL to livechatinc using sent_event api to keep the CDN URL alive
     * https://developers.livechatinc.com/docs/messaging/agent-chat-api/#file
     * https://developers.livechatinc.com/docs/messaging/agent-chat-api/#send-event
     */
    public function sendFileToLiveChatInc(Request $request): JsonResponse
    {
        $chatId = $request->id;
        //Get Thread ID From Customer Live Chat
        $customer = CustomerLiveChat::where('customer_id', $chatId)->first();
        if ($customer != '' && $customer != null) {
            $thread = $customer->thread;
        } else {
            return response()->json(['status' => 'errors', 'errorMsg' => 'Thread not found'], 200);
        }

        $fileUploadResult = self::uploadFileToLiveChatInc($request);

        if (! $fileUploadResult) {
            //There is some error, we didn't get the CDN file path
            return response()->json(['status' => 'errors', 'errorMsg' => 'Error uploading file'], 200);
        } else {
            $fileCDNPath = $fileUploadResult['CDNPath'];
            $filename = $fileUploadResult['filename'];
        }

        $postData = ['chat_id' => $thread, 'event' => ['type' => 'file', 'url' => $fileCDNPath, 'recipients' => 'all']];
        $postData = json_encode($postData);

        $postURL = 'https://api.livechatinc.com/v3.1/agent/action/send_event';

        $result = self::curlCall($postURL, $postData, 'application/json');
        if ($result['err']) {
            return response()->json(['status' => 'errors', 'errorMsg' => $result['err']], 403);
        } else {
            $response = json_decode($result['response']);
            if (isset($response->error)) {
                return response()->json(['status' => 'errors', $response], 403);
            } else {
                return response()->json(['status' => 'success', 'filename' => $filename, 'fileCDNPath' => $fileCDNPath, 'responseData' => $response], 200);
            }
        }
    }

    public static function sendFileMessageEvent($postData): JsonResponse
    {
        $cdnPath = $postData['event']['url'];
        $postData = json_encode($postData, true);
        $postURL = 'https://api.livechatinc.com/v3.1/agent/action/send_event';
        $result = self::curlCall($postURL, $postData, 'application/json');
        if ($result['err']) {
            return response()->json(['status' => 'errors', 'errorMsg' => $result['err']], 403);
        } else {
            $response = json_decode($result['response']);
            if (isset($response->error)) {
                return response()->json(['status' => 'errors', $response], 403);
            } else {
                return response()->json(['status' => 'success', 'filename' => $cdnPath, 'fileCDNPath' => $cdnPath, 'responseData' => $response], 200);
            }
        }
    }

    public static function getDomain($url)
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }

        return false;
    }

    public function saveToken(Request $request): JsonResponse
    {
        if ($request->accessToken) {
            $storedCache = Cache::get('key');
            if ($storedCache) {
                if ($storedCache != $request->accessToken) {
                    try {
                        Cache::put('key', $request->accessToken, $request->seconds);
                    } catch (Exception $e) {
                        Cache::add('key', $request->accessToken, $request->seconds);
                    }
                }
            } else {
                try {
                    Cache::put('key', $request->accessToken, $request->seconds);
                } catch (Exception $e) {
                    Cache::add('key', $request->accessToken, $request->seconds);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'AccessToken saved'], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'AccessToken cannot be saved'], 500);
    }

    public function attachImage(Request $request): RedirectResponse
    {
        $customerid = $request->get('customer_id', 0);
        $livechat = CustomerLiveChat::where('customer_id', $customerid)->where('thread', '!=', '')->first();

        if ($livechat) {
            if ($request->images != null) {
                $images = json_decode($request->images, true);
                $images = array_filter($images);
                if (! empty($images)) {
                    $medias = Media::whereIn('id', array_unique($images))->get();
                    if (! $medias->isEmpty()) {
                        foreach ($medias as $media) {
                            $cdn = self::useAbsPathUpload($media->getAbsolutePath());
                            if (! $cdn == false) {
                                $postData = [
                                    'chat_id' => $livechat->thread,
                                    'event' => [
                                        'type' => 'file',
                                        'url' => $cdn['CDNPath'],
                                        'recipients' => 'all',
                                    ],
                                ];
                                self::sendFileMessageEvent($postData);
                            }
                        }
                    }
                }
            }
        }

        return redirect()->to(route('livechat.get.chats').'?open_chat=true');
    }

    /**
     * Get tickets from livechat inc and put them as unread messages
     *
     * https://developers.livechatinc.com/docs/management/configuration-api/v2.0/#tickets
     * https://api.livechatinc.com/tickets?assigned=0
     * dal:ZP6x3Uc3QMa9W-Ve4sp86A
     */
    public function getLiveChatIncTickets()
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();
        $url = 'https://api.livechatinc.com/v2/tickets';

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic NmY0M2ZkZDUtOTkwMC00OWY4LWI4M2ItZThkYzg2ZmU3ODcyOmRhbDp0UkFQdWZUclFlLVRkQUI4Y2pFajNn',
            ],
        ]);

        $response = curl_exec($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, LiveChatController::class, 'getLiveChatIncTickets');
        $result = json_decode($response, true);
        if (! empty($result['tickets'])) {
            return $result['tickets'];
        } else {
            return false;
        }
    }

    /**  Created By Maulik Jadvani
     * function to Get tickets list.
     *
     * @param request
     * @return -all tickets list
     */
    public function tickets(Request $request)
    {

        $selectArray[] = 'tickets.*';
        $selectArray[] = 'users.name AS assigned_to_name';
        $selectArray[] = 'customers.is_auto_simulator AS customer_auto_simulator';
        $query = Tickets::query();
        $query = $query->leftjoin('users', 'users.id', '=', 'tickets.assigned_to');
        $query = $query->leftjoin('customers', 'customers.id', '=', 'tickets.customer_id');

        $query = $query->select($selectArray);

        if ($request->ticket_id != '') {
            $query = $query->whereIn('ticket_id', $request->ticket_id);
        }

        if ($request->users_id != '') {
            $query = $query->whereIn('assigned_to', $request->users_id);
        }

        if ($request->term != '') {
            $query = $query->whereIn('tickets.name', $request->term);
        }

        if ($request->user_email != '') {
            $query = $query->whereIn('tickets.email', $request->user_email);
        }

        if ($request->user_message != '') {
            $query = $query->where('tickets.message', 'LIKE', '%'.$request->user_message.'%');
        }

        if ($request->search_country != '') {
            $query = $query->where('tickets.country', 'LIKE', '%'.$request->search_country.'%');
        }

        if ($request->search_order_no != '') {
            $query = $query->where('tickets.order_no', 'LIKE', '%'.$request->search_order_no.'%');
        }

        if ($request->search_phone_no != '') {
            $query = $query->where('tickets.phone_no', 'LIKE', '%'.$request->search_phone_no.'%');
        }

        if ($request->serach_inquiry_type != '') {
            $query = $query->where('tickets.type_of_inquiry', 'LIKE', '%'.$request->serach_inquiry_type.'%');
        }

        // Use for search by source tof ticket
        if ($request->search_source != '') {
            $query = $query->where('tickets.source_of_ticket', 'LIKE', '%'.$request->search_source.'%');
        }

        if ($request->status_id != '') {
            $query = $query->whereIn('status_id', $request->status_id);
        }

        if ($request->date != '') {
            $query = $query->whereDate('tickets.created_at', $request->date);
        }

        $pageSize = Setting::get('pagination', 25);
        if ($pageSize == '') {
            $pageSize = 1;
        }
        $query = $query->groupBy('tickets.ticket_id');
        $data = $query->orderByDesc('created_at')->paginate($pageSize)->appends(request()->except(['page']));

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'livechat-tickets')->first();

        $dynamicColumnsToShowLt = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowLt = json_decode($hideColumns, true);
        }

        $statusList = TicketStatuses::all()->pluck('name', 'id');
        $authUser = \Auth::user();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('livechat.partials.ticket-list', compact('data', 'dynamicColumnsToShowLt', 'authUser', 'statusList'))->with('i', ($request->input('page', 1) - 1) * $pageSize)->render(),
                'links' => (string) $data->links(),
                'count' => $data->total(),
            ], 200);
        }
        $taskstatus = TicketStatuses::get();
        $emailAddressArr = EmailAddress::all();
        $todoCategories = TodoCategory::get();
        $todoStatus = TodoStatus::all();

        return view('livechat.tickets', compact('data', 'taskstatus', 'dynamicColumnsToShowLt', 'emailAddressArr', 'statusList', 'authUser', 'todoCategories', 'todoStatus'))->with('i', ($request->input('page', 1) - 1) * $pageSize);
    }

    public function getEmailreplies(Request $request)
    {
        $id = $request->id;
        $emailReplies = Reply::where('category_id', $id)->orderBy('id')->get();

        return json_encode($emailReplies);
    }

    public function columnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'livechat-tickets')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'livechat-tickets';
            $column->column_name = json_encode($request->column_lt);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'livechat-tickets';
            $column->column_name = json_encode($request->column_lt);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = TicketStatuses::find($key);
            $bugstatus->ticket_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function createTickets(Request $request): JsonResponse
    {
        $data = [];
        $data['ticket_id'] = 'T'.date('YmdHis');
        $customer = Customer::find($request->ticket_customer_id);
        $email = null;
        $name = null;
        if ($customer) {
            $name = $customer->name;
            $email = $customer->email;
        }
        $data['date'] = date('Y-m-d H:i:s');
        $data['name'] = $name;
        $data['email'] = $email;
        $data['customer_id'] = $request->ticket_customer_id;
        $data['source_of_ticket'] = $request->source_of_ticket;
        $data['subject'] = $request->ticket_subject;
        $data['message'] = $request->ticket_message;
        $data['assigned_to'] = $request->ticket_assigned_to;
        $data['status_id'] = $request->ticket_status_id;
        Tickets::create($data);

        return response()->json(['ticket created successfully', 'code' => 200, 'status' => 'success']);
    }

    public function createCredits(Request $request): JsonResponse
    {
        $customer_id = $request->credit_customer_id;
        $credit = $request->credit;
        $type = $request->credit_type;
        $customer = Customer::find($customer_id);
        $currency = $request->get('currency', 'EUR');
        $customercurrency = ! empty($customer->currency) ? $customer->currency : 'EUR';

        if (! isset($customer->credit) || $customer->credit == null || $customer->credit == '') {
            $customer->credit = 0;
        }

        $calc_credit = 0;

        $currentcredit = Currency::convert($customer->credit, $currency, $customercurrency);

        if ($credit < 0) {
            if ($currentcredit == 0) {
                $calc_credit = $currentcredit + ($credit);
            } else {
                $credit = str_replace('-', '', $credit);
                $calc_credit = $currentcredit - $credit;
            }
        } else {
            $calc_credit = $currentcredit + $credit;
        }

        if ($customer) {
            if ($customer->store_website_id != null && $customer->platform_id != null) {
                $websiteDetails = StoreWebsite::where('id', $customer->store_website_id)->select('magento_url', 'api_token')->first();
                if ($websiteDetails != null && $websiteDetails['magento_url'] != null && $websiteDetails['api_token'] != null && $request->credit > 0) {
                    $websiteUrl = $websiteDetails['magento_url'];
                    $api_token = $websiteDetails['api_token'];
                    $bits = parse_url($websiteUrl);
                    if (isset($bits['host'])) {
                        $web = $bits['host'];
                        if (! Str::contains($websiteUrl, 'www')) {
                            $web = 'www.'.$bits['host'];
                        }
                        $websiteUrl = 'https://'.$web;
                        $post = [
                            'transaction[amount]' => $request->credit,
                            'transaction[type]' => 'add',
                            'transaction[summary]' => 'test',
                            'transaction[suppress_notification]' => '1',
                        ];
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        $ch = curl_init();
                        $url = $websiteUrl.'/rest/V1/swarming/credits/add-store-credit/'.$customer->platform_id;
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);

                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

                        $headers = [];
                        $headers[] = 'Authorization: Bearer '.$api_token;
                        $headers[] = 'Cookie: PHPSESSID=m261vs1h58pprpilkr720tqtog; country_code=IN; private_content_version=6f2c1b0f27956af2f0669199874878ed';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        LogRequest::log($startTime, $url, 'POST', json_encode($post), json_decode($result), $httpcode, LiveChatController::class, 'createCredits');
                        curl_close($ch);
                        $status = 'failure';
                        if ($result == '[]') {
                            $customer->credit = $calc_credit;
                            $customer->currency = $currency;
                            $customer->save();

                            //Add history and Send mail
                            CreditHistory::create(
                                [
                                    'customer_id' => $customer_id,
                                    'model_id' => $customer_id,
                                    'model_type' => Customer::class,
                                    'used_credit' => $credit,
                                    'used_in' => 'Added with '.$currency,
                                    'type' => $type,
                                ]
                            );
                            try {
                                $emailClass = (new SendIssueCredit($customer))->build();
                            } catch (Exception $e) {
                                $post = [
                                    'customer-id' => $customer->id,
                                ];
                                CreditLog::create(['customer_id' => $customer->id, 'request' => json_encode($post), 'response' => $e->getMessage(), 'status' => 'failure']);

                                return response()->json(['msg' => 'issue with mailing template', 'code' => 400, 'status' => 'error']);
                            }

                            if ($emailClass) {
                                $email = Email::create([
                                    'model_id' => $customer->id,
                                    'model_type' => Customer::class,
                                    'from' => $emailClass->fromMailer,
                                    'to' => $customer->email,
                                    'subject' => $emailClass->subject,
                                    'message' => $emailClass->render(),
                                    'template' => 'issue-credit',
                                    'additional_data' => '',
                                    'status' => 'pre-send',
                                    'store_website_id' => null,
                                ]);

                                CreditEmailLog::create([
                                    'customer_id' => $customer_id,
                                    'subject' => $emailClass->subject,
                                    'from' => $emailClass->fromMailer,
                                    'to' => $customer->email,
                                    'message' => $emailClass->render(),
                                ]);
                                try {
                                    SendEmail::dispatch($email)->onQueue('send_email');
                                } catch (Exception $e) {
                                    $post = [
                                        'email-id' => $email->id,
                                        'customer-id' => $customer->id,
                                    ];
                                    CreditLog::create(['customer_id' => $customer->id, 'request' => json_encode($post), 'response' => $e->getMessage(), 'status' => 'failure']);

                                    return response()->json(['msg' => 'mail not sent', 'code' => 400, 'status' => 'error']);
                                }
                            } else {
                                $post = [
                                    'customer-id' => $customer->id,
                                ];
                                CreditLog::create(['customer_id' => $customer->id, 'request' => json_encode($post), 'response' => 'email template not found', 'status' => 'failure']);

                                return response()->json(['msg' => 'email template not found', 'code' => 400, 'status' => 'error']);
                            }

                            $status = 'success';
                            CreditLog::create(['customer_id' => $customer->id, 'request' => json_encode($post), 'response' => json_encode($result), 'status' => $status]);

                            return response()->json(['msg' => 'credit updated successfully', 'code' => 200, 'status' => 'success']);
                        } else {
                            CreditLog::create(['customer_id' => $customer->id, 'request' => json_encode($post), 'response' => json_encode($result), 'status' => $status]);

                            return response()->json(['msg' => json_encode($result), 'code' => 400, 'status' => 'error']);
                        }
                    } else {
                        $post = [
                            'customer-id' => $customer->id,
                        ];
                        CreditLog::create(['customer_id' => $customer->id, 'request' => json_encode($post), 'response' => 'store website and platform not found', 'status' => 'failure']);

                        return response()->json(['msg' => 'store website and platform not found', 'code' => 400, 'status' => 'error']);
                    }
                }
            } else {
                $post = [
                    'customer-id' => $customer->id,
                ];
                CreditLog::create(['customer_id' => $customer->id, 'request' => json_encode($post), 'response' => 'allocate store website id and platform id not found', 'status' => 'failure']);

                return response()->json(['msg' => 'Allocate store website id and platform id before proceeding', 'code' => 400, 'status' => 'error']);
            }
        } else {
            return response()->json(['msg' => 'customer not found', 'code' => 400, 'status' => 'error']);
        }
    }

    public function customerCreditLogs($customerId): JsonResponse
    {
        $logs = CreditLog::where('customer_id', $customerId)->orderByDesc('id')->get();
        $creditLogs = '';
        foreach ($logs as $log) {
            $creditLogs .= "<tr>
            <td width='25%'>".$log['created_at']."</td>
            <td width='25%'>".$log['request']."</td>
            <td width='25%'>".$log['response']."</td>
            <td width='25%'>".$log['status']."</td>
            <td width='25%'><a class='repush-credit-balance' href='/customer/credit-repush/".$log['id']."'>Repush</td>
            </tr>";
        }

        return response()->json(['data' => $creditLogs, 'code' => 200, 'status' => 'success']);
    }

    public function customerCreditHistories($customerId): JsonResponse
    {
        $histories = CreditHistory::where('customer_id', $customerId)->orderByDesc('id')->get();
        $creditHistories = '';
        foreach ($histories as $history) {
            $creditHistories .= "<tr>
            <td width='25%'>".$history['id']."</td>
            <td width='25%'>".$history['used_credit']."</td>
            <td width='25%'>".$history['used_in']."</td>
            <td width='25%'>".$history['type']."</td>
            <td width='25%'>".$history['created_at'].'</td>
            </tr>';
        }

        return response()->json(['data' => $creditHistories, 'code' => 200, 'status' => 'success']);
    }

    public function getCreditsData(Request $request): JsonResponse
    {
        $customer = Customer::find($request->customer_id);
        if ($customer->credit == null || $customer->credit == '') {
            $currentcredit = 0;
        } else {
            $currentcredit = $customer->credit;
        }
        $credits = CreditHistory::where('customer_id', $request->customer_id)->orderByDesc('id')->get();

        return response()->json(['data' => $credits, 'currentcredit' => $currentcredit, 'status' => 'success']);
    }

    public function getTicketsData(Request $request): JsonResponse
    {
        $tickets = Tickets::where('customer_id', $request->customer_id)->with('ticketStatus')->get();

        return response()->json(['data' => $tickets, 'status' => 'success']);
    }

    public function sendEmail(SendEmailLiveChatRequest $request): RedirectResponse
    {

        $tickets = Tickets::find($request->ticket_id);
        if (! isset($tickets->id)) {
            //
        }
        $ticketIdString = '#'.$tickets->ticket_id;
        $fromEmail = 'buying@amourint.com';

        if ($request->from_mail) {
            $mail = EmailAddress::where('id', $request->from_mail)->first();
            if ($mail) {
                $fromEmail = $mail->from_address;
                $fromName = $mail->from_name;
                $config = config('mail');
                unset($config['sendmail']);
                $configExtra = [
                    'driver' => $mail->driver,
                    'host' => $mail->host,
                    'port' => $mail->port,
                    'from' => [
                        'address' => $mail->from_address,
                        'name' => $mail->from_name,
                    ],
                    'encryption' => $mail->encryption,
                    'username' => $mail->username,
                    'password' => $mail->password,
                ];
                Config::set('mail', array_merge($config, $configExtra));
                (new \Illuminate\Mail\MailServiceProvider(app()))->register();
            }
        }
        $message = $request->message;
        if ($tickets->email != '') {
            $file_paths = [];

            if ($tickets->lang_code != '' && $tickets->lang_code != 'en') {
                $message = TranslationHelper::translate('en', $tickets->lang_code, $request->message);
            }

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $filename = $file->getClientOriginalName();

                    $file->storeAs('documents', $filename, 'files');

                    $file_paths[] = "documents/$filename";
                }
            }

            $cc = $bcc = [];
            $emails[] = $tickets->email;

            if ($request->has('cc')) {
                $cc = array_values(array_filter($request->cc));
            }
            if ($request->has('bcc')) {
                $bcc = array_values(array_filter($request->bcc));
            }

            if (is_array($emails) && ! empty($emails)) {
                $to = array_shift($emails);
                $cc = array_merge($emails, $cc);

                $mail = Mail::to($to);

                if ($cc) {
                    $mail->cc($cc);
                }
                if ($bcc) {
                    $mail->bcc($bcc);
                }

                $mail->send(new PurchaseEmail($request->subject.$ticketIdString, $request->message, $file_paths, ['from' => $fromEmail]));
            } else {
                return redirect()->back()->withErrors('Please select an email');
            }

            $params = [
                'model_id' => $tickets->id,
                'model_type' => Tickets::class,
                'from' => $fromEmail,
                'to' => $tickets->email,
                'seen' => 1,
                'subject' => $request->subject.$ticketIdString,
                'message' => $message,
                'message_en' => $request->message,
                'template' => 'customer-simple',
                'additional_data' => json_encode(['attachment' => $file_paths]),
                'cc' => $cc ?: null,
                'bcc' => $bcc ?: null,
            ];

            Email::create($params);

            return redirect()->back()->withSuccess('You have successfully sent an email!');
        }
    }

    public function fetchEmailsOnTicket($ticketId): View
    {

        $emails = Email::where('model_id', $ticketId)->where('model_type', Tickets::class)->get();
        $email_status = EmailStatus::get();

        return view('livechat.partials.ticket-email', compact('emails', 'email_status'));
    }

    public function AssignTicket(AssignTicketLiveChatRequest $request): RedirectResponse
    {

        $users_id = $request->users_id;

        $tickets = Tickets::find($request->id);
        if (isset($tickets->id) && $tickets->id > 0) {
            $tickets->assigned_to = $users_id;
            $tickets->save();

            return redirect()->back()->withSuccess('Tickets has been successfully Assigned.');
        } else {
            return redirect()->back()->withErrors('something wrong please try to again Assigned Tickets.');
        }
    }

    public function TicketStatus(TicketStatusLiveChatRequest $request): RedirectResponse
    {

        $name = $request->name;
        $TicketStatusObj = TicketStatuses::where(['name' => $name])->first();
        if (isset($TicketStatusObj->id) && $TicketStatusObj->id > 0) {
        } else {
            TicketStatuses::create(['name' => $name]);
        }

        return redirect()->back()->withSuccess('Ticket Status has been successfully Added.');
    }

    public function ChangeStatus(Request $request): JsonResponse
    {
        if ($request->status != '' && $request->id != '') {
            $tickets = Tickets::find($request->id);
            if (isset($tickets->id) && $tickets->id > 0) {
                $tickets->status_id = $request->status;
                $tickets->save();
            }
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function ChangeDate(Request $request): JsonResponse
    {
        if ($request->date != '' && $request->id != '') {
            $tickets = Tickets::find($request->id);
            if (isset($tickets->id) && $tickets->id > 0) {
                $tickets->resolution_date = $request->date;
                $tickets->save();
            }
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function sendBrodcast(Request $request): JsonResponse
    {
        $ids = $request->selected_tasks;
        if (! empty($ids)) {
            foreach ($ids as $id) {
                // started to send message
                $requestData = new Request;
                $requestData->setMethod('POST');
                $requestData->request->add(['ticket_id' => $id, 'message' => $request->message, 'status' => 1]);
                app(WhatsAppController::class)->sendMessage($requestData, 'ticket');
            }

            return response()->json(['code' => 200, 'message' => 'Message has been sent to all selected ticket']);
        }

        return response()->json(['code' => 500, 'message' => 'Please select atleast one ticket']);
    }

    public function delete_tickets(Request $request): JsonResponse
    {
        Tickets::find($request->id)->delete();

        return response()->json(['code' => 200, 'message' => 'Record Delete ticket']);
    }

    public function creditRepush(Request $request, $id): JsonResponse
    {
        $creditLog = CreditLog::find($id);
        if ($creditLog) {
            $customer = Customer::find($creditLog->customer_id);
            if ($customer->store_website_id != null and $customer->platform_id != null) {
                $websiteDetails = StoreWebsite::where('id', $customer->store_website_id)->select('magento_url', 'api_token')->first();
                if ($websiteDetails != null && $websiteDetails['magento_url'] != null && $websiteDetails['api_token'] != null) {
                    $websiteUrl = $websiteDetails['magento_url'];
                    $api_token = $websiteDetails['api_token'];
                    $bits = parse_url($websiteUrl);
                    if (isset($bits['host'])) {
                        $web = $bits['host'];
                        if (! Str::contains($websiteUrl, 'www')) {
                            $web = 'www.'.$bits['host'];
                        }
                        $websiteUrl = 'https://'.$web;
                        $post = json_decode($creditLog->request, true);
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        $ch = curl_init();
                        $url = $websiteUrl.'/rest/V1/swarming/credits/add-store-credit/'.$customer->platform_id;
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

                        $headers = [];
                        $headers[] = 'Authorization: Bearer '.$api_token;
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result = curl_exec($ch);
                        $jsonResult = json_decode($result); //response decode
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        LogRequest::log($startTime, $url, 'GET', json_encode($post), $jsonResult, $httpcode, LiveChatController::class, 'creditRepush');
                        curl_close($ch);
                        $status = 'failure';
                        $code = 500;
                        if ($result == '[]') {
                            $code = 200;
                            $status = 'success';
                        }

                        if (isset($jsonResult->message)) {
                            $code = 500;
                            $status = 'failure';
                        }

                        CreditLog::create(['customer_id' => $customer->id, 'request' => json_encode($post), 'response' => json_encode($result), 'status' => $status]);
                    }

                    return response()->json(['message' => (isset($jsonResult->message)) ? $jsonResult->message : 'Credit updated successfully', 'code' => $code, 'status' => $status]);
                }

                return response()->json(['message' => 'Store website not found', 'code' => 500, 'status' => 'failed']);
            } else {
                return response()->json(['message' => 'Customer store website not found', 'code' => 500, 'status' => 'failed']);
            }
        } else {
            return response()->json(['message' => 'Credit log not found', 'code' => 500, 'status' => 'failed']);
        }
    }

    /*
    * getLiveChatCouponCode : Get coupon code according to store website
    */
    public function getLiveChatCouponCode(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $customer = Customer::find($request->get('customer_id'));
            $couponsData = CouponCodeRules::where('store_website_id', $customer->store_website_id)->where('coupon_code', '!=', '')->where('is_active', 1)->get()->toArray();
            if ($couponsData) {
                return response()->json([
                    'data' => $couponsData,
                ], 200);
            }

            return response()->json([
                'data' => [],
            ], 200);
        }
    }

    /*
   * Send mail method
   */
    public function sendLiveChatCouponCode(Request $request): JsonResponse
    {
        $ruleId = $request->rule_id;
        $couponCodeRule = CouponCodeRules::find($ruleId);
        $customerId = $request->customer_id;
        $customerData = Customer::find($customerId);
        CouponCodeRuleLog::create([
            'rule_id' => $ruleId,
            'coupon_code' => $couponCodeRule->coupon_code,
            'log_type' => 'send_to_user_intiate',
            'message' => 'Sending coupon mail to '.$customerData->email,
        ]);
        $emailAddress = EmailAddress::where('store_website_id', $couponCodeRule->store_website_id)->first();
        $mailData['receiver_email'] = $customerData->email;
        $mailData['sender_email'] = $emailAddress->from_address;
        $mailData['coupon'] = $couponCodeRule->coupon_code;
        $mailData['model_id'] = $ruleId;
        $mailData['model_class'] = CouponCodeRules::class;
        $mailData['store_website_id'] = $couponCodeRule->store_website_id;
        $emailClass = (new AddCoupon($mailData))->build();
        $email = Email::create([
            'model_id' => $ruleId,
            'model_type' => CouponCodeRules::class,
            'from' => $emailAddress->from_address,
            'to' => $customerData->email,
            'subject' => $emailClass->subject,
            'message' => $emailClass->render(),
            'template' => 'coupons',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => $couponCodeRule->store_website_id,
            'is_draft' => 0,
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');
        CouponCodeRuleLog::create([
            'rule_id' => $ruleId,
            'coupon_code' => $couponCodeRule->coupon_code,
            'log_type' => 'send_mail',
            'message' => 'Mail was sent to '.$customerData->email,
        ]);

        return response()->json([
            'message' => 'coupon send successully',
        ], 200);
    }

    public function customerInfo(Request $request): JsonResponse
    {
        $liveChatData = CustomerLiveChat::find($request->id);
        $threadId = '';
        $returnVal = '';
        if ($liveChatData->customer_id) {
            $chatId = $liveChatData->customer_id;

            //getting customer name from chat
            $customer = Customer::findorfail($chatId);
            $email = $customer->email;

            $threadId = $liveChatData->thread;

            if ($threadId) {
                $postURL = 'https://api.livechatinc.com/v3.1/agent/action/get_customers';

                $postData = ['filters' => ['email' => ['values' => [$email]]]];
                $postData = json_encode($postData);

                $returnVal = '';
                $result = self::curlCall($postURL, $postData, 'application/json');

                if ($result['err']) {
                    $returnVal = false;
                } else {
                    $response = json_decode($result['response']);
                    if (isset($response->error)) {
                        $returnVal = false;
                    } else {
                        $returnVal = $response->customers[0];
                    }
                }
            }
        }

        return response()->json(['status' => 'success', 'threadId' => $threadId, 'customerInfo' => $returnVal], 200);
    }

    public function watsonJourney(request $request): View
    {
        $watsonJourney = WatsonChatJourney::orderByDesc('id')->paginate(10)->appends(request()->except(['page']));
        if (count($watsonJourney) != 0) {
            foreach ($watsonJourney as $value) {
                $id = null;
                if (! empty($value->chat_id)) {
                    $id = $value->chat_id;
                } else {
                    $id = $value->chat_message_id;
                }
                $senderDeatilsId = ChatMessage::where('id', $id)->first();
                if (! empty($senderDeatilsId)) {
                    $sender_id = $senderDeatil = null;
                    if (! empty($senderDeatilsId->vendor_id)) {
                        $sender_id = $senderDeatilsId->vendor_id;
                        $senderDeatil = Vendor::select('name', 'phone', 'id')->where('id', $sender_id)->first();
                    } elseif (! empty($senderDeatilsId->supplier_id)) {
                        $sender_id = $senderDeatilsId->supplier_id;
                        $senderDeatil = Supplier::select('supplier as name', 'phone', 'id')->where('id', $sender_id)->first();
                    } elseif (! empty($senderDeatilsId->user_id)) {
                        $sender_id = $senderDeatilsId->user_id;
                        $senderDeatil = User::select('name', 'phone', 'id')->where('id', $sender_id)->first();
                    } elseif (! empty($senderDeatilsId->customer_id)) {
                        $sender_id = $senderDeatilsId->customer_id;
                        $senderDeatil = Customer::select('name', 'phone', 'id')->where('id', $sender_id)->first();
                    }
                }
                if (! empty($senderDeatil)) {
                    $value->sender_name = $senderDeatil->name;
                    $value->sender_phone = $senderDeatil->phone;
                } else {
                    $value->sender_name = '';
                    $value->sender_phone = '';
                }
            }
        }

        return view('livechat.journey', compact('watsonJourney'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function ajax(request $request): JsonResponse
    {
        $unsetvalue = null;
        $watsonJourney = WatsonChatJourney::where(function ($query) use ($request) {
            if (isset($request->apply_id) && isset($request->term) && $request->term != '' && $request->apply_id != '') {
                if ($request->apply_id == 1) {
                    $query = $query->where('reply', 'LIKE', '%'.$request->term.'%');
                } elseif ($request->apply_id == 4) {
                    $query = $query->where('message_received', 'LIKE', '%'.$request->term.'%');
                } elseif ($request->apply_id == 5) {
                    $query = $query->where('chat_entered', 'LIKE', '%'.$request->term.'%');
                } elseif ($request->apply_id == 6) {
                    $query = $query->where('reply_found_in_database', 'LIKE', '%'.$request->term.'%');
                } elseif ($request->apply_id == 7) {
                    $query = $query->where('reply_searched_in_watson', 'LIKE', '%'.$request->term.'%');
                } elseif ($request->apply_id == 8) {
                    $query = $query->where('response_sent_to_cusomer', 'LIKE', '%'.$request->term.'%');
                }
            }
        })->orderByDesc('id')->paginate(10);
        if (count($watsonJourney) != 0) {
            foreach ($watsonJourney as $key => $value) {
                $id = null;
                if (! empty($value->chat_id)) {
                    $id = $value->chat_id;
                } else {
                    $id = $value->chat_message_id;
                }
                $senderDeatilsId = ChatMessage::select('id', 'vendor_id', 'supplier_id', 'user_id', 'customer_id')->where('id', $id)->first();
                if (! empty($senderDeatilsId)) {
                    $sender_id = $senderDeatil = null;
                    if (! empty($senderDeatilsId->vendor_id)) {
                        $sender_id = $senderDeatilsId->vendor_id;
                        if ($request->apply_id == 2) {
                            $senderDeatil = Vendor::select('name', 'phone', 'id')->where('id', $sender_id)->where('name', 'LIKE', '%'.$request->term.'%')->first();
                        } elseif ($request->apply_id == 3) {
                            $senderDeatil = Vendor::select('name', 'phone', 'id')->where('id', $sender_id)->where('phone', 'LIKE', '%'.$request->term.'%')->first();
                        } else {
                            $senderDeatil = Vendor::select('name', 'phone', 'id')->where('id', $sender_id)->first();
                        }
                    } elseif (! empty($senderDeatilsId->supplier_id)) {
                        $sender_id = $senderDeatilsId->supplier_id;
                        if ($request->apply_id == 2) {
                            $senderDeatil = Supplier::select('supplier as name', 'phone', 'id')->where('id', $sender_id)->where('supplier', 'LIKE', '%'.$request->term.'%')->first();
                        } elseif ($request->apply_id == 3) {
                            $senderDeatil = Supplier::select('supplier as name', 'phone', 'id')->where('id', $sender_id)->where('phone', 'LIKE', '%'.$request->term.'%')->first();
                        } else {
                            $senderDeatil = Supplier::select('supplier as name', 'phone', 'id')->where('id', $sender_id)->first();
                        }
                    } elseif (! empty($senderDeatilsId->user_id)) {
                        $sender_id = $senderDeatilsId->user_id;
                        if ($request->apply_id == 2) {
                            $senderDeatil = User::select('name', 'phone', 'id')->where('id', $sender_id)->where('name', 'LIKE', '%'.$request->term.'%')->first();
                        } elseif ($request->apply_id == 3) {
                            $senderDeatil = User::select('name', 'phone', 'id')->where('id', $sender_id)->where('phone', 'LIKE', '%'.$request->term.'%')->first();
                        } else {
                            $senderDeatil = User::select('name', 'phone', 'id')->where('id', $sender_id)->first();
                        }
                    } elseif (! empty($senderDeatilsId->customer_id)) {
                        $sender_id = $senderDeatilsId->customer_id;
                        if ($request->apply_id == 2) {
                            $senderDeatil = Customer::select('name', 'phone', 'id')->where('id', $sender_id)->where('name', 'LIKE', '%'.$request->term.'%')->first();
                        } elseif ($request->apply_id == 3) {
                            $senderDeatil = Customer::select('name', 'phone', 'id')->where('id', $sender_id)->where('phone', 'LIKE', '%'.$request->term.'%')->first();
                        } else {
                            $senderDeatil = Customer::select('name', 'phone', 'id')->where('id', $sender_id)->first();
                        }
                    }
                }
                if (! empty($senderDeatil)) {
                    $value->sender_name = $senderDeatil->name;
                    $value->sender_phone = $senderDeatil->phone;
                } else {
                    $value->sender_name = '';
                    $value->sender_phone = '';
                    if ($request->apply_id == 2 || $request->apply_id == 3) {
                        $unsetvalue[] = $key;
                    }
                }
            }
            unset($watsonJourney[$unsetvalue]);
        }

        return response()->json([
            'livechat' => view('livechat.partials.list-journey', compact('watsonJourney'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
            'links' => (string) $watsonJourney->render(),
            'count' => $watsonJourney->total(),
        ], 200);
    }

    //DEVTASK-22731 - START
    public function updateTicket(Request $request): JsonResponse
    {
        ChatMessage::where(['id' => $request->id])->update(['message' => $request->message, 'message_en' => $request->message]);

        return response()->json(['status' => true, 'message' => 'Data updated successfully']);
    }

    public function approveTicket(Request $request): JsonResponse
    {
        ChatMessage::where(['id' => $request->id])->update(['send_to_tickets' => 1]);

        return response()->json(['status' => true, 'message' => 'Data updated successfully']);
    }

    public function ticketData(Request $request): JsonResponse
    {
        $result = ChatMessage::where('ticket_id', $request->ticket_id)->orderByDesc('id')->get();
        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                $sendTo = '-';
                $customer = Customer::select('name', 'id')->where('id', $value->customer_id)->first();
                if (! empty($customer)) {
                    $sendTo = $customer->name;
                }
                $sopdata = Sop::where(['chat_message_id' => $value->id])->first();
                if (! empty($sopdata)) {
                    $result[$key]['sop_name'] = $sopdata->name;
                    $result[$key]['sop_category'] = $sopdata->category;
                    $result[$key]['sop_content'] = $sopdata->content;
                } else {
                    $result[$key]['sop_name'] = null;
                    $result[$key]['sop_category'] = null;
                    $result[$key]['sop_content'] = null;
                }

                $check_replies = ChatbotReply::where(['chat_id' => $value->id])->first();
                if (! empty($check_replies)) {
                    $result[$key]['out'] = true;
                    $result[$key]['datetime'] = 'From '.$check_replies->reply_from.' To '.$sendTo.' On '.Carbon::parse($value->created_at)->format('Y-m-d H:i A');
                } else {
                    $result[$key]['out'] = false;
                    $result[$key]['datetime'] = 'From ERP'.' To '.$sendTo.' On '.Carbon::parse($value->created_at)->format('Y-m-d H:i A');
                }
            }
        }

        return response()->json(['data' => $result, 'count' => $result->count()]);
    }
    //DEVTASK-22731 - END

    public function loadReplyCategories()
    {
        $reply_categories = ReplyCategory::where('parent_id', 0)->orderBy('name')->get();
        $strResult = '';
        foreach ($reply_categories as $category) {
            $strResult .= "<option value='".$category->id."'>".$category->name.'</option>';
        }

        return $strResult;
    }

    public function chatHeaderModel(): JsonResponse
    {
        try {
            $customerLiveChatIds = CustomerLiveChat::with('customer')
                ->join(DB::raw('(Select max(id) as id from customer_live_chats group by customer_id) LatestMessage'), function ($join) {
                    $join->on('customer_live_chats.id', '=', 'LatestMessage.id');
                })
                ->groupBy('customer_id')->orderBy('created_at', 'desc')->get();
            $allReplyCategories = cache()->remember('ReplyCategory::all', 60 * 60 * 24 * 7, function () {
                return ReplyCategory::all();
            });
            $websiteData = StoreWebsite::pluck('website', 'id');
            $html = view('partials.modals.quick-chatbox-window', compact('customerLiveChatIds', 'allReplyCategories', 'websiteData'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }
}
