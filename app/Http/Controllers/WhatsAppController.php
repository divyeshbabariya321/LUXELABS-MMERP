<?php

namespace App\Http\Controllers;

use App\Account;
use App\ApiKey;
use App\AutoCompleteMessage;
use App\AutoReply;
use App\BlockWebMessageList;
use App\Blogger;
use App\BrandFans;
use App\BroadcastImage;
use App\ChatbotMessageLog;
use App\ChatbotMessageLogResponse;
use App\ChatbotReply;
use App\ChatMessage;
use App\ChatMessagesQuickData;
use App\ColdLeads;
use App\CommunicationHistory;
use App\Contact;
use App\Customer;
use App\CustomerCharity;
use App\DeveloperTask;
use App\Document;
use App\DocumentSendHistory;
use App\Dubbizle;
use App\Email;
use App\ErpLeads;
use App\Helpers\DevelopmentHelper;
use App\Helpers\HubstaffTrait;
use App\Helpers\InstantMessagingHelper;
use App\Helpers\MessageHelper;
use App\Helpers\TranslationHelper;
use App\Http\Requests\SendMessageWhatsAppRequest;
use App\Http\Requests\SendToAllWhatsAppRequest;
use App\Hubstaff\HubstaffActivitySummary;
use App\Imports\CustomerNumberImport;
use App\ImQueue;
use App\Instruction;
use App\Issue;
use App\Jobs\SendEmail;
use App\Lawyer;
use App\Learning;
use App\LegalCase;
use App\LogChatMessage;
use App\LogRequest;
use App\Mails\Manual\PurchaseExport;
use App\Marketing\WhatsappBusinessAccounts;
use App\Marketing\WhatsappConfig;
use App\Mediables;
use App\Message;
use App\MessageQueue;
use App\Old;
use App\Order;
use App\PaymentReceipt;
use App\Product;
use App\QuickSellGroup;
use App\Services\BulkCustomerMessage\KeywordsChecker;
use App\Services\Products\SendImagesOfProduct;
use App\Services\SlackNotificationService;
use App\Setting;
use App\StoreWebsite;
use App\SuggestedProductList;
use App\Supplier;
use App\SupplierTranslateHistory;
use App\Task;
use App\Tickets;
use App\User;
use App\Vendor;
use App\WatsonChatJourney;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
// use IlluminUserFeedbackStatuspport\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Image;
use Maatwebsite\Excel\Facades\Excel;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;

class WhatsAppController extends FindByNumberController
{
    use HubstaffTrait;

    const MEDIA_PDF_CHUNKS = 50;

    const AUTO_LEAD_SEND_PRICE = 281;

    private $githubClient;

    public function __construct()
    {
        $this->githubClient = new GuzzleClient([
            'auth' => [config('env.GITHUB_USERNAME'), config('env.GITHUB_TOKEN')],
        ]);
        $this->init(config('env.HUBSTAFF_SEED_PERSONAL_TOKEN'));
    }

    /**
     * Incoming message URL for whatsApp
     */
    public function incomingMessage(Request $request, GuzzleClient $client): \Illuminate\Http\Response
    {
        $data = $request->json()->all();
        $data = $this->mapForWassenger($data);
        if ($data['event'] == 'INBOX') {
            $to = $data['to'];
            $from = $data['from'];
            $text = $data['text'];
            $user = $this->findUserByNumber($from);
            $supplier = $this->findSupplierByNumber($from);
            $customer = $this->findCustomerByNumber($from);

            $params = [
                'number' => $from,
            ];

            if ($user) {
                $params = $this->modifyParamsWithMessage($params, $data);

                $params['erp_user'] = $user->id;

                $params = $this->modifyParamsWithMessage($params, $data);

                if (array_key_exists('message', $params) && (preg_match_all("/#([\d]+)/i", $params['message'], $match))) {
                    $params['task_id'] = $match[1][0];
                }

                $message = ChatMessage::create($params);
                $model_type = 'user';
                $model_id = $user->id;

                if (array_key_exists('task_id', $params)) {
                    $this->sendRealTime($message, 'task_'.$match[1][0], $client);
                } else {
                    $this->sendRealTime($message, 'erp_user_'.$user->id, $client);
                }
            }

            if ($supplier) {
                $params['erp_user'] = null;
                $params['task_id'] = null;
                $params['supplier_id'] = $supplier->id;

                $params = $this->modifyParamsWithMessage($params, $data, $supplier->id);
                $message = ChatMessage::create($params);
                $model_type = 'supplier';
                $model_id = $supplier->id;

                $this->sendRealTime($message, 'supplier_'.$supplier->id, $client);
            }

            if ($customer) {
                $params['erp_user'] = null;
                $params['supplier_id'] = null;
                $params['task_id'] = null;
                $params['customer_id'] = $customer->id;

                $params = $this->modifyParamsWithMessage($params, $data);
                $message = ChatMessage::create($params);

                if (isset($params['message']) && $params['message']) {
                    (new KeywordsChecker)->assignCustomerAndKeywordForNewMessage($params['message'], $customer);
                }

                $model_type = 'customers';
                $model_id = $customer->id;
                $customer->update([
                    'whatsapp_number' => $to,
                ]);

                $this->sendRealTime($message, 'customer_'.$customer->id, $client);

                if (Setting::get('forward_messages') == 1) {
                    if (Setting::get('forward_start_date') != null && Setting::get('forward_end_date') != null) {
                        $time = Carbon::now();
                        $start_date = Carbon::parse(Setting::get('forward_start_date'));
                        $end_date = Carbon::parse(Setting::get('forward_end_date'));

                        if ($time->between($start_date, $end_date, true)) {
                            $forward_users_ids = json_decode(Setting::get('forward_users'));
                            $second_message = '';

                            if ($message->message == null) {
                                $forwarded_message = "FORWARDED from $customer->name";
                                $second_message = $message->media_url;
                            } else {
                                $forwarded_message = "FORWARDED from $customer->name - ".$message->message;
                            }

                            foreach ($forward_users_ids as $user_id) {
                                $user = User::find($user_id);

                                $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $forwarded_message, false, $message->id);

                                if ($second_message != '') {
                                    $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, false, $message->id);
                                }
                            }
                        }
                    } else {
                        $forward_users_ids = json_decode(Setting::get('forward_users'));
                        $second_message = '';

                        if ($message->message == null) {
                            $forwarded_message = "FORWARDED from $customer->name";
                            $second_message = $message->media_url;
                        } else {
                            $forwarded_message = "FORWARDED from $customer->name - ".$message->message;
                        }

                        foreach ($forward_users_ids as $user_id) {
                            $user = User::find($user_id);

                            $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $forwarded_message, false, $message->id);

                            if ($second_message != '') {
                                $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, false, $message->id);
                            }
                        }
                    }
                }

                // Auto DND Keyword Stop Added By Satyam
                if (array_key_exists('message', $params) && strtoupper($params['message']) == 'DND' || strtoupper($params['message']) == 'STOP') {
                    if ($customer = Customer::find($params['customer_id'])) {
                        $customer->do_not_disturb = 1;
                        $customer->save();
                        Log::channel('customerDnd')->debug('(Customer ID '.$customer->id.' line '.$customer->name.' '.$customer->number.': Added To DND');

                        $dnd_params = [
                            'number' => null,
                            'user_id' => 6,
                            'approved' => 1,
                            'status' => 9,
                            'customer_id' => $customer->id,
                            'message' => AutoReply::where('type', 'auto-reply')->where('keyword', 'customer-dnd')->first()->reply,
                        ];

                        $auto_dnd_message = ChatMessage::create($dnd_params);

                        $this->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $dnd_params['message'], false, $auto_dnd_message->id);
                    }
                }

                // Auto Instruction
                if (array_key_exists('message', $params) && (preg_match('/price/i', $params['message']) || preg_match('/you photo/i', $params['message']) || preg_match('/pp/i', $params['message']) || preg_match('/how much/i', $params['message']) || preg_match('/cost/i', $params['message']) || preg_match('/rate/i', $params['message']))) {
                    if ($customer = Customer::find($params['customer_id'])) {
                        $two_hours = Carbon::now()->subHours(2);
                        $latest_broadcast_message = ChatMessage::where('customer_id', $customer->id)->where('created_at', '>', $two_hours)->where('status', 8)->orderByDesc('id')->first();

                        if ($latest_broadcast_message) {
                            if (! $latest_broadcast_message->isSentBroadcastPrice()) {
                                if ($latest_broadcast_message->hasMedia(config('constants.media_tags'))) {
                                    $selected_products = [];

                                    foreach ($latest_broadcast_message->getMedia(config('constants.media_tags')) as $image) {
                                        $image_key = $image->getKey();
                                        $mediable_type = 'BroadcastImage';

                                        $broadcast = BroadcastImage::with('Media')
                                            ->whereRaw("broadcast_images.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key AND mediables.mediable_type LIKE '%$mediable_type%')")
                                            ->first();

                                        if ($broadcast) {
                                            $brod_products = json_decode($broadcast->products, true);

                                            if (count($brod_products) > 0) {
                                                foreach ($brod_products as $brod_pro) {
                                                    $selected_products[] = $brod_pro;
                                                }
                                            }
                                        }
                                    }

                                    if (isset($broadcast)) {
                                        if (! empty($selected_products)) {
                                            foreach ($selected_products as $pid) {
                                                $product = Product::where('id', $pid)->first();
                                                $quick_lead = ErpLeads::create([
                                                    'customer_id' => $customer->id,
                                                    'lead_status_id' => 3,
                                                    'product_id' => $pid,
                                                    'store_website_id' => 15,
                                                    'brand_id' => $product ? $product->brand : null,
                                                    'category_id' => $product ? $product->category : null,
                                                    'brand_segment' => $product && $product->brands ? $product->brands->brand_segment : null,
                                                    'color' => $customer->color,
                                                    'size' => $customer->size,
                                                    'type' => 'whatsapp-incoming-message',
                                                    'created_at' => Carbon::now(),
                                                ]);
                                            }
                                            $requestData = new Request;
                                            $requestData->setMethod('POST');
                                            $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $selected_products]);

                                            app(LeadsController::class)->sendPrices($requestData);
                                        }

                                        CommunicationHistory::create([
                                            'model_id' => $latest_broadcast_message->id,
                                            'model_type' => ChatMessage::class,
                                            'type' => 'broadcast-prices',
                                            'method' => 'whatsapp',
                                        ]);
                                    } else {
                                        //
                                    }
                                }
                            }
                        }

                        Instruction::create([
                            'customer_id' => $customer->id,
                            'instruction' => 'Please send the prices',
                            'category_id' => 1,
                            'assigned_to' => 7,
                            'assigned_from' => 6,
                        ]);
                    }
                }

                // Auto Replies
                $auto_replies = AutoReply::all();

                foreach ($auto_replies as $auto_reply) {
                    if (array_key_exists('message', $params) && $params['message'] != '') {
                        $keyword = $auto_reply->keyword;

                        if (preg_match("/{$keyword}/i", $params['message'])) {
                            $temp_params = $params;
                            $temp_params['message'] = $auto_reply->reply;
                            $temp_params['status'] = 1;

                            ChatMessage::create($temp_params);

                            $this->sendRealTime($message, 'customer_'.$customer->id, $client);
                        }
                    }
                }
            } else {
                $params['customer_id'] = null;
            }

            if (! isset($user) && ! isset($purchase) && ! isset($customer)) {
                $modal_type = 'leads';
                $validate_phone['phone'] = $from;

                $validator = Validator::make($validate_phone, [
                    'phone' => 'unique:customers,phone',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => 'error', 'message' => 'Validation failed.']);
                } else {
                    $customer = new Customer;
                    $customer->name = $from;
                    $customer->phone = $from;
                    $customer->rating = 2;
                    $customer->save();

                    $lead = ErpLeads::create([
                        'customer_id' => $customer->id,
                        'store_website_id' => 15,
                        'lead_status_id' => 1,
                        'type' => 'whatsapp-incoming-message',
                    ]);

                    $params['lead_id'] = $lead->id;
                    $params['customer_id'] = $customer->id;
                    $params = $this->modifyParamsWithMessage($params, $data);
                    $message = ChatMessage::create($params);
                    $model_type = 'leads';
                    $model_id = $lead->id;

                    $this->sendRealTime($message, 'customer_'.$customer->id, $client);
                }
            }

            // Auto Respond
            $today_date = Carbon::now()->format('Y-m-d');
            $time = Carbon::now();
            $start_time = Setting::get('start_time');
            $start_time_exploded = explode(':', $start_time);
            $end_time = Setting::get('end_time');
            $end_time_exploded = explode(':', $end_time);
            $morning = Carbon::create($time->year, $time->month, $time->day, $start_time_exploded[0], $start_time_exploded[1], 0);
            $not_morning = Carbon::create($time->year, $time->month, $time->day, 0, 0, 0);
            $evening = Carbon::create($time->year, $time->month, $time->day, $end_time_exploded[0], $end_time_exploded[1], 0);
            $not_evening = Carbon::create($time->year, $time->month, $time->day, 23, 59, 0);
            $saturday = Carbon::now()->endOfWeek()->subDay()->format('Y-m-d');
            $sunday = Carbon::now()->endOfWeek()->format('Y-m-d');

            $chat_messages_query = ChatMessage::where('customer_id', $params['customer_id'])->whereBetween('created_at', [$morning, $evening])->whereNotNull('number');
            $chat_messages_count = $chat_messages_query->count();

            $chat_messages_evening_query = ChatMessage::where('customer_id', $params['customer_id'])->where(function ($query) use ($not_morning, $morning, $evening, $not_evening) {
                $query->whereBetween('created_at', [$not_morning, $morning])->orWhereBetween('created_at', [$evening, $not_evening]);
            })->whereNotNull('number');
            $chat_messages_evening_count = $chat_messages_evening_query->count();

            if ($chat_messages_count == 1) {
                $chat_messages_query_first = $chat_messages_query->first();
            }

            if ($chat_messages_evening_count == 1) {
                $chat_messages_evening_query_first = $chat_messages_evening_query->first();
            }

            if ($chat_messages_count == 1 && (isset($chat_messages_query_first) && $chat_messages_query_first->id == $message->id) && ($saturday != $today_date && $sunday != $today_date)) {
                $customer = Customer::find($params['customer_id']);
                $params = [
                    'number' => null,
                    'user_id' => 6,
                    'approved' => 1,
                    'status' => 9,
                    'customer_id' => $params['customer_id'],
                ];

                if ($time->between($morning, $evening, true)) {
                    $params['message'] = AutoReply::where('type', 'auto-reply')->where('keyword', 'work-hours-greeting')->first()->reply;

                    sleep(1);
                    $additional_message = ChatMessage::create($params);
                    $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, false, $additional_message->id);
                }
            } else {
                if (($chat_messages_evening_count == 1 && (isset($chat_messages_evening_query_first) && $chat_messages_evening_query_first->id == $message->id)) || ($chat_messages_count == 1 && ($saturday == $today_date || $sunday == $today_date))) {
                    $customer = Customer::find($params['customer_id']);

                    $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'office-closed-message')->first();

                    $auto_message = str_replace('/{start_time}/i', $start_time, $auto_reply->reply);
                    $auto_message = str_replace('/{end_time}/i', $end_time, $auto_message);

                    $params = [
                        'number' => null,
                        'user_id' => 6,
                        'approved' => 1,
                        'status' => 9,
                        'customer_id' => $params['customer_id'],
                        'message' => $auto_message,
                    ];

                    sleep(1);
                    $additional_message = ChatMessage::create($params);
                    $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, false, $additional_message->id);
                }
            }
        } else {
            $custom_data = json_decode($data['custom_data'], true);

            $chat_message = ChatMessage::find($custom_data['chat_message_id']);

            if ($chat_message) {
                $chat_message->sent = 1;
                $chat_message->save();
            }
        }

        return response()->noContent(200);
    }

    public function mapForWassenger($data)
    {
        if (isset($data['event'])) {
            if ($data['event'] == 'message:in:new') {
                $data['event'] = 'INBOX';
            }
        }
        $data['messages'][] = $data['data'];
        unset($data['data']);

        if (isset($data['messages'])) {
            $data['messages'][0]['fromMe'] = false;
            $data['messages'][0]['author'] = $data['messages'][0]['from'];
            $data['instanceId'] = $data['messages'][0]['id'];
        }

        return $data;
    }

    public function sendRealTime($message, $model_id, $client, $customFile = null): \Illuminate\Http\Response
    {
        $realtime_params = [
            'realtime_id' => $model_id,
            'id' => $message->id,
            'number' => $message->number,
            'assigned_to' => $message->assigned_to ?? '',
            'created_at' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
            'approved' => $message->approved ?? 0,
            'status' => $message->status ?? 0,
            'user_id' => $message->user_id ?? 0,
            'erp_user' => $message->erp_user ?? 0,
            'sent' => $message->sent ?? 0,
            'resent' => $message->resent ?? 0,
            'error_status' => $message->error_status ?? 0,
        ];

        // attach custom image or file here if not want to send original
        $mediaUrl = ($customFile && ! empty($customFile)) ? $customFile : $message->media_url;

        if ($mediaUrl) {
            $realtime_params['media_url'] = $mediaUrl;
            $headers = get_headers($mediaUrl, 1);
            $realtime_params['content_type'] = is_string($headers['Content-Type']) ? $headers['Content-Type'] : $headers['Content-Type'][1];
        }

        if ($message->message) {
            $realtime_params['message'] = $message->message;
        }

        $response = $client->post('https://sololuxury.co/deliver-message', [
            'form_params' => $realtime_params,
        ]);

        return response('success');
    }

    public function incomingMessageNew(Request $request, GuzzleClient $client): \Illuminate\Http\Response
    {
        $data = $request->json()->all();

        if ($data['event'] == 'message:in:new') {
            $to = str_replace('+', '', $data['data']['toNumber']);
            $from = str_replace('+', '', $data['data']['fromNumber']);
            $text = $data['data']['body'];
            $user = $this->findUserByNumber($from);
            $supplier = $this->findSupplierByNumber($from);
            $customer = $this->findCustomerByNumber($from);
            $dubbizle = $this->findDubbizleByNumber($from);

            $params = [
                'number' => $from,
                'message' => '',
            ];

            if ($data['data']['type'] == 'text') {
                $params['message'] = $text;
            } else {
                if ($data['data']['type'] == 'image') {
                    $image_data = $data['data']['media']['preview']['image'];
                    $image_path = public_path().'/uploads/temp_image.png';
                    $img = Image::make(base64_decode($image_data))->encode('jpeg')->save($image_path);

                    $media = MediaUploader::fromSource($image_path)->upload();

                    File::delete('uploads/temp_image.png');
                }
            }

            if ($user) {
                $instruction = Instruction::where('assigned_to', $user->id)->latest()->first();

                if ($instruction) {
                    $myRequest = new Request;
                    $myRequest->setMethod('POST');
                    $myRequest->request->add(['remark' => $params['message'], 'id' => $instruction->id, 'module_type' => 'instruction', 'user_name' => 'User from Whatsapp']);

                    app(TaskModuleController::class)->addRemark($myRequest);
                }

                NotificationQueueController::createNewNotification([
                    'message' => $params['message'],
                    'timestamps' => ['+0 minutes'],
                    'model_type' => Instruction::class,
                    'model_id' => $instruction->id,
                    'user_id' => '6',
                    'sent_to' => $instruction->assigned_from,
                    'role' => '',
                ]);

                $params['erp_user'] = $user->id;

                if ($params['message'] != '' && (preg_match_all("/TASK ID ([\d]+)/i", $params['message'], $match))) {
                    $params['task_id'] = $match[1][0];
                }

                $params = $this->modifyParamsWithMessage($params, $data);
                $message = ChatMessage::create($params);
                $model_type = 'user';
                $model_id = $user->id;

                if (array_key_exists('task_id', $params)) {
                    $this->sendRealTime($message, 'task_'.$match[1][0], $client);
                } else {
                    $this->sendRealTime($message, 'erp_user_'.$user->id, $client);
                }
            }

            if ($supplier) {
                $params['erp_user'] = null;
                $params['task_id'] = null;
                $params['supplier_id'] = $supplier->id;

                $message = ChatMessage::create($params);
                $model_type = 'supplier';
                $model_id = $supplier->id;

                $this->sendRealTime($message, 'supplier_'.$supplier->id, $client);
            }

            if ($dubbizle) {
                $params['erp_user'] = null;
                $params['task_id'] = null;
                $params['supplier_id'] = null;
                $params['dubbizle_id'] = $dubbizle->id;

                $message = ChatMessage::create($params);
                $model_type = 'dubbizle';
                $model_id = $dubbizle->id;

                $this->sendRealTime($message, 'dubbizle_'.$dubbizle->id, $client);
            }

            if ($customer) {
                $params['erp_user'] = null;
                $params['supplier_id'] = null;
                $params['task_id'] = null;
                $params['dubbizle_id'] = null;
                $params['customer_id'] = $customer->id;

                $message = ChatMessage::create($params);

                if ($params['message']) {
                    (new KeywordsChecker)->assignCustomerAndKeywordForNewMessage($params['message'], $customer);
                }

                $model_type = 'customers';
                $model_id = $customer->id;
                $customer->update([
                    'whatsapp_number' => $to,
                ]);

                $this->sendRealTime($message, 'customer_'.$customer->id, $client);

                if (Setting::get('forward_messages') == 1) {
                    if (Setting::get('forward_start_date') != null && Setting::get('forward_end_date') != null) {
                        $time = Carbon::now();
                        $start_date = Carbon::parse(Setting::get('forward_start_date'));
                        $end_date = Carbon::parse(Setting::get('forward_end_date'));

                        if ($time->between($start_date, $end_date, true)) {
                            $forward_users_ids = json_decode(Setting::get('forward_users'));
                            $second_message = '';

                            if ($message->message == null) {
                                $forwarded_message = "FORWARDED from $customer->name";
                                $second_message = $message->media_url;
                            } else {
                                $forwarded_message = "FORWARDED from $customer->name - ".$message->message;
                            }

                            foreach ($forward_users_ids as $user_id) {
                                $user = User::find($user_id);

                                $this->sendWithNewApi($user->phone, $user->whatsapp_number, $forwarded_message, null, $message->id);

                                if ($second_message != '') {
                                    $this->sendWithNewApi($user->phone, $user->whatsapp_number, null, $second_message, $message->id);
                                }
                            }
                        }
                    } else {
                        $forward_users_ids = json_decode(Setting::get('forward_users'));
                        $second_message = '';

                        if ($message->message == null) {
                            $forwarded_message = "FORWARDED from $customer->name";
                            $second_message = $message->media_url;
                        } else {
                            $forwarded_message = "FORWARDED from $customer->name - ".$message->message;
                        }

                        foreach ($forward_users_ids as $user_id) {
                            $user = User::find($user_id);

                            $this->sendWithNewApi($user->phone, $user->whatsapp_number, $forwarded_message, null, $message->id);

                            if ($second_message != '') {
                                $this->sendWithNewApi($user->phone, $user->whatsapp_number, null, $second_message, $message->id);
                            }
                        }
                    }
                }

                // Auto DND
                if (array_key_exists('message', $params) && strtoupper($params['message']) == 'DND') {
                    if ($customer = Customer::find($params['customer_id'])) {
                        $customer->do_not_disturb = 1;
                        $customer->save();
                        Log::channel('customerDnd')->debug('(Customer ID '.$customer->id.' line '.$customer->name.' '.$customer->number.': Added To DND');

                        $dnd_params = [
                            'number' => null,
                            'user_id' => 6,
                            'approved' => 1,
                            'status' => 9,
                            'customer_id' => $customer->id,
                            'message' => AutoReply::where('type', 'auto-reply')->where('keyword', 'customer-dnd')->first()->reply,
                        ];

                        $auto_dnd_message = ChatMessage::create($dnd_params);

                        $this->sendWithNewApi($customer->phone, $customer->whatsapp_number, $dnd_params['message'], null, $auto_dnd_message->id);
                    }
                }

                // Auto Instruction
                if (array_key_exists('message', $params) && (preg_match('/price/i', $params['message']) || preg_match('/you photo/i', $params['message']) || preg_match('/pp/i', $params['message']) || preg_match('/how much/i', $params['message']) || preg_match('/cost/i', $params['message']) || preg_match('/rate/i', $params['message']))) {
                    if ($customer = Customer::find($params['customer_id'])) {
                        $two_hours = Carbon::now()->subHours(2);
                        $latest_broadcast_message = ChatMessage::where('customer_id', $customer->id)->where('created_at', '>', $two_hours)->where('status', 8)->latest()->first();

                        if ($latest_broadcast_message) {
                            if (! $latest_broadcast_message->isSentBroadcastPrice()) {
                                if ($latest_broadcast_message->hasMedia(config('constants.media_tags'))) {
                                    $selected_products = [];

                                    foreach ($latest_broadcast_message->getMedia(config('constants.media_tags')) as $image) {
                                        $image_key = $image->getKey();
                                        $mediable_type = 'BroadcastImage';

                                        $broadcast = BroadcastImage::with('Media')
                                            ->whereRaw("broadcast_images.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key AND mediables.mediable_type LIKE '%$mediable_type%')")
                                            ->first();

                                        if ($broadcast) {
                                            $brod_products = json_decode($broadcast->products, true);

                                            if (count($brod_products) > 0) {
                                                foreach ($brod_products as $brod_pro) {
                                                    $selected_products[] = $brod_pro;
                                                }
                                            }
                                        }
                                    }

                                    if (isset($broadcast)) {
                                        foreach ($selected_products as $pid) {
                                            $product = Product::where('id', $pid)->first();
                                            $quick_lead = ErpLeads::create([
                                                'customer_id' => $customer->id,
                                                'lead_status_id' => 3,
                                                'store_website_id' => 15,
                                                'product_id' => $pid,
                                                'type' => 'whatsapp-incoming-message-new',
                                                'brand_id' => $product ? $product->brand : null,
                                                'category_id' => $product ? $product->category : null,
                                                'brand_segment' => $product && $product->brands ? $product->brands->brand_segment : null,
                                                'color' => $customer->color,
                                                'size' => $customer->size,
                                                'created_at' => Carbon::now(),
                                            ]);
                                        }

                                        $requestData = new Request;
                                        $requestData->setMethod('POST');
                                        $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $selected_products]);

                                        app(LeadsController::class)->sendPrices($requestData);

                                        CommunicationHistory::create([
                                            'model_id' => $latest_broadcast_message->id,
                                            'model_type' => ChatMessage::class,
                                            'type' => 'broadcast-prices',
                                            'method' => 'whatsapp',
                                        ]);
                                    } else {
                                        //
                                    }
                                }
                            }
                        }

                        Instruction::create([
                            'customer_id' => $customer->id,
                            'instruction' => 'Please send the prices',
                            'category_id' => 1,
                            'assigned_to' => 7,
                            'assigned_from' => 6,
                        ]);
                    }
                }

                // Auto Replies
                $auto_replies = AutoReply::all();

                foreach ($auto_replies as $auto_reply) {
                    if (array_key_exists('message', $params) && $params['message'] != '') {
                        $keyword = $auto_reply->keyword;

                        if (preg_match("/{$keyword}/i", $params['message'])) {
                            $temp_params = $params;
                            $temp_params['message'] = $auto_reply->reply;
                            $temp_params['status'] = 1;

                            ChatMessage::create($temp_params);

                            $this->sendRealTime($message, 'customer_'.$customer->id, $client);
                        }
                    }
                }
            }

            if (! isset($user) && ! isset($purchase) && ! isset($customer)) {
                $modal_type = 'leads';
                $validate_phone['phone'] = $from;

                $validator = Validator::make($validate_phone, [
                    'phone' => 'unique:customers,phone',
                ]);

                if ($validator->fails()) {
                    return response()->json(['code' => 400, 'message' => 'Validation failed']);
                } else {
                    $customer = new Customer;
                    $customer->name = $from;
                    $customer->phone = $from;
                    $customer->rating = 2;
                    $customer->save();

                    $lead = ErpLeads::create([
                        'customer_id' => $customer->id,
                        'store_website_id' => 15,
                        'lead_status_id' => 1,
                        'type' => 'whatsapp-incoming-message-new',
                    ]);

                    $params['lead_id'] = $lead->id;
                    $params['customer_id'] = $customer->id;

                    $message = ChatMessage::create($params);
                    $model_type = 'leads';
                    $model_id = $lead->id;

                    $this->sendRealTime($message, 'customer_'.$customer->id, $client);
                }
            }

            // Auto Respond
            $today_date = Carbon::now()->format('Y-m-d');
            $time = Carbon::now();
            $start_time = Setting::get('start_time');
            $start_time_exploded = explode(':', $start_time);
            $end_time = Setting::get('end_time');
            $end_time_exploded = explode(':', $end_time);
            $morning = Carbon::create($time->year, $time->month, $time->day, $start_time_exploded[0], $start_time_exploded[1], 0);
            $not_morning = Carbon::create($time->year, $time->month, $time->day, 0, 0, 0);
            $evening = Carbon::create($time->year, $time->month, $time->day, $end_time_exploded[0], $end_time_exploded[1], 0);
            $not_evening = Carbon::create($time->year, $time->month, $time->day, 23, 59, 0);
            $saturday = Carbon::now()->endOfWeek()->subDay()->format('Y-m-d');
            $sunday = Carbon::now()->endOfWeek()->format('Y-m-d');

            $chat_messages_query = ChatMessage::where('customer_id', $params['customer_id'])->whereBetween('created_at', [$morning, $evening])->whereNotNull('number');
            $chat_messages_count = $chat_messages_query->count();

            $chat_messages_evening_query = ChatMessage::where('customer_id', $params['customer_id'])->where(function ($query) use ($not_morning, $morning, $evening, $not_evening) {
                $query->whereBetween('created_at', [$not_morning, $morning])->orWhereBetween('created_at', [$evening, $not_evening]);
            })->whereNotNull('number');
            $chat_messages_evening_count = $chat_messages_evening_query->count();

            if ($chat_messages_count == 1) {
                $chat_messages_query_first = $chat_messages_query->first();
            }

            if ($chat_messages_evening_count == 1) {
                $chat_messages_evening_query_first = $chat_messages_evening_query->first();
            }

            if ($chat_messages_count == 1 && (isset($chat_messages_query_first) && $chat_messages_query_first->id == $message->id) && ($saturday != $today_date && $sunday != $today_date)) {
                $customer = Customer::find($params['customer_id']);
                $params = [
                    'number' => null,
                    'user_id' => 6,
                    'approved' => 1,
                    'status' => 9,
                    'customer_id' => $params['customer_id'],
                ];

                if ($time->between($morning, $evening, true)) {
                    $params['message'] = AutoReply::where('type', 'auto-reply')->where('keyword', 'work-hours-greeting')->first()->reply;

                    sleep(1);
                    $additional_message = ChatMessage::create($params);
                    $this->sendWithNewApi($message->customer->phone, $customer->whatsapp_number, $additional_message->message, null, $additional_message->id);
                }
            } else {
                if (($chat_messages_evening_count == 1 && (isset($chat_messages_evening_query_first) && $chat_messages_evening_query_first->id == $message->id)) || ($chat_messages_count == 1 && ($saturday == $today_date || $sunday == $today_date))) {
                    $customer = Customer::find($params['customer_id']);

                    $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'office-closed-message')->first();

                    $auto_message = str_replace('/{start_time}/i', $start_time, $auto_reply->reply);
                    $auto_message = str_replace('/{end_time}/i', $end_time, $auto_message);

                    $params = [
                        'number' => null,
                        'user_id' => 6,
                        'approved' => 1,
                        'status' => 9,
                        'customer_id' => $params['customer_id'],
                        'message' => $auto_message,
                    ];

                    sleep(1);
                    $additional_message = ChatMessage::create($params);
                    $this->sendWithNewApi($message->customer->phone, $customer->whatsapp_number, $additional_message->message, null, $additional_message->id);
                }
            }

            if ($data['data']['type'] == 'image') {
                $media->move('chatmessage/'.floor($message->id / config('constants.image_per_folder')));
                $message->attachMedia($media, config('constants.media_tags'));
            }
        } else {
            $custom_data = json_decode($data['custom_data'], true);

            $chat_message = ChatMessage::find($custom_data['chat_message_id']);

            if ($chat_message) {
                $chat_message->sent = 1;
                $chat_message->save();
            }
        }

        return response('success');
    }

    public function webhook(Request $request, GuzzleClient $client)
    {
        // Get json object
        $data = $request->json()->all();
        $needToSendLeadPrice = false;
        $isReplied = false;

        $data = $this->mapForWassenger($data);

        // Log incoming webhook
        Log::channel('chatapi')->debug('Webhook: '.json_encode($data));
        // Check for ack
        if (array_key_exists('ack', $data)) {
            //
        }

        // Check for messages
        if (! array_key_exists('messages', $data)) {
            return response()->json('ACK', 200);
        }

        // Loop over messages
        foreach ($data['messages'] as $chatapiMessage) {
            $quoted_message_id = null;
            // Convert false and true text to false and true
            if ($chatapiMessage['fromMe'] === 'false') {
                $chatapiMessage['fromMe'] = false;
            }
            if ($chatapiMessage['fromMe'] === 'true') {
                $chatapiMessage['fromMe'] = true;
            }

            $parentMessage = null;

            try {
                // check if quotedMsgId is available, if available then we will search for parent message
                if (isset($chatapiMessage['quotedMsgId'])) {
                    $parentMessage = ChatMessage::where('unique_id', $chatapiMessage['quotedMsgId'])->first();
                    if ($parentMessage) {
                        $quoted_message_id = $parentMessage->id;
                    }
                }
            } catch (Exception $e) {
                //continue
            }

            WatsonChatJourney::updateOrCreate(['chat_message_id' => $quoted_message_id], ['chat_entered' => 1]);
            WatsonChatJourney::updateOrCreate(['chat_message_id' => $quoted_message_id], ['message_received' => 1]);

            // Set default parameters
            $from = str_replace('@c.us', '', $chatapiMessage['author']);
            $instanceId = $data['instanceId'];
            $text = $chatapiMessage['body'];
            $contentType = $chatapiMessage['type'];
            $numberPath = substr($from, 0, 3).'/'.substr($from, 3, 1);

            // Check if message already exists
            $chatMessage = ChatMessage::where('unique_id', $chatapiMessage['id'])->first();
            if ($chatMessage != null) {
                //continue;
            }

            // Find connection with this number in our database
            if ($chatapiMessage['fromMe'] == true) {
                $searchNumber = str_replace('@c.us', '', $chatapiMessage['chatId']);
            } else {
                $searchNumber = $from;
            }

            // Find objects by number
            $supplier = $this->findSupplierByNumber($searchNumber);
            $vendor = $this->findVendorByNumber($searchNumber);
            $user = $this->findUserByNumber($searchNumber);
            $dubbizle = $this->findDubbizleByNumber($searchNumber);
            $contact = $this->findContactByNumber($searchNumber);
            $customer = $this->findCustomerByNumber($searchNumber);

            // check the message related to the supplier
            $sendToSupplier = false;
            if (! empty($text)) {
                $matchSupplier = explode('-', $text);
                if (
                    isset($matchSupplier[0]) && $matchSupplier[0] == 'S'
                    && isset($matchSupplier[1]) && is_numeric($matchSupplier[1])
                ) {
                    $sendToSupplier = true;
                    $supplier = Supplier::find($matchSupplier[1]);
                }
            }
            if (! empty($customer)) {
                //
            }

            if (! empty($supplier) && $contentType !== 'image') {
                $supplierDetails = is_object($supplier) ? Supplier::find($supplier->id) : $supplier;
                $language = $supplierDetails->language;
                if ($language != null) {
                    $fromLang = $language;
                    $toLang = 'en';

                    if ($sendToSupplier) {
                        $fromLang = 'en';
                        $toLang = $language;
                    }

                    $result = TranslationHelper::translate($fromLang, $toLang, $text);
                    if ($sendToSupplier) {
                        $text = $result;
                    } else {
                        $text = $result.' -- '.$text;
                    }
                }
            }
            $originalMessage = $text;
            // Set params
            $params = [
                'number' => $from,
                'unique_id' => $chatapiMessage['id'],
                'message' => '',
                'media_url' => null,
                'approved' => $chatapiMessage['fromMe'] ? 1 : 0,
                'status' => $chatapiMessage['fromMe'] ? 2 : 0,
                'contact_id' => null,
                'erp_user' => null,
                'supplier_id' => null,
                'task_id' => null,
                'dubizzle_id' => null,
                'vendor_id' => null,
                'customer_id' => null,
                'quoted_message_id' => $quoted_message_id,
            ];

            try {
                // check if time exist then convert and assign it
                if (isset($chatapiMessage['time'])) {
                    $params['created_at'] = date('Y-m-d H:i:s', $chatapiMessage['time']);
                }
            } catch (Exception $e) {
                //If the date format is causing issue from whats app script messages
                $params['created_at'] = $chatapiMessage['time'];
            }

            // Check if the message is a URL
            if (filter_var($text, FILTER_VALIDATE_URL)) {
                if (substr($text, 0, 23) == 'https://firebasestorage') {
                    // Try to download the image
                    try {
                        // Get file extension
                        $extension = str_replace("#\?.*#", '', pathinfo($text, PATHINFO_EXTENSION))."\n";

                        // Set tmp file
                        $filePath = public_path().'/uploads/tmp_'.rand(0, 100000).'.'.trim($extension);

                        // Copy URL to file path
                        copy($text, $filePath);

                        // Upload media
                        $media = MediaUploader::fromSource($filePath)->useFilename(uniqid(true, true))->toDisk('s3')->toDirectory('chat-messages/'.$numberPath)->upload();

                        // Delete the file
                        unlink($filePath);

                        // Update media URL
                        $params['media_url'] = getMediaUrl($media);
                        $params['message'] = isset($chatapiMessage['caption']) ? $chatapiMessage['caption'] : '';
                    } catch (Exception $exception) {
                        Log::error($exception);
                        //
                    }
                } else {
                    try {
                        $extension = str_replace("#\?.*#", '', pathinfo($text, PATHINFO_EXTENSION))."\n";
                        // Set tmp file
                        $filePath = public_path().'/uploads/tmp_'.rand(0, 100000).'.'.trim($extension);
                        // Copy URL to file path
                        copy($text, $filePath);
                        // Upload media
                        $media = MediaUploader::fromSource($filePath)->useFilename(uniqid(true, true))->toDisk('s3')->toDirectory('chat-messages/'.$numberPath)->upload();
                        // Delete the file
                        unlink($filePath);
                        // Update media URL
                        $params['media_url'] = getMediaUrl($media);
                        $params['message'] = isset($chatapiMessage['caption']) ? $chatapiMessage['caption'] : '';
                    } catch (Exception $exception) {
                        Log::error($exception);
                        $params['message'] = $text;
                    }
                }
            } else {
                $params['message'] = $text;
            }

            // From me? Only store, nothing else
            if ($chatapiMessage['fromMe'] == true) {
                // Set objects
                $params['erp_user'] = isset($user->id) ? $user->id : null;
                $params['supplier_id'] = isset($supplier->id) ? $supplier->id : null;
                $params['task_id'] = null;
                $params['dubbizle_id'] = isset($dubbizle->id) ? $dubbizle->id : null;
                $params['vendor_id'] = isset($vendor->id) && ! isset($customer->id) ? $vendor->id : null;
                $params['customer_id'] = isset($customer->id) ? $customer->id : null;

                // Remove number
                $params['number'] = null;

                // Set unique ID
                $params['unique_id'] = $chatapiMessage['id'];

                // Check for duplicate vendor message
                if (isset($vendor->id)) {
                    // Find duplicate message
                    $duplicateChatMessage = ChatMessage::where('vendor_id', $vendor->id)->where('message', $params['message'])->first();

                    // Set vendor ID to null if message is found
                    if ($duplicateChatMessage != null) {
                        $params['vendor_id'] = null;
                    }
                }
                // Create message
                $message = ChatMessage::create($params);

                // Continue to the next record
                continue;
            }

            $userId = $supplierId = $contactId = $vendorId = $dubbizleId = $customerId = null;

            if ($user != null) {
                $userId = $user->id;
            }

            if ($contact != null) {
                $contactId = $contact->id;
            }

            if ($supplier != null) {
                $supplierId = $supplier->id;
            }

            if ($vendor != null) {
                $vendorId = $vendor->id;
            }

            if ($dubbizle != null) {
                $dubbizleId = $dubbizle->id;
            }

            if ($customer != null) {
                $customerId = $customer->id;
            }

            $params['user_id'] = $userId;
            $params['contact_id'] = $contactId;
            $params['supplier_id'] = $supplierId;
            $params['vendor_id'] = $vendorId;
            $params['dubbizle_id'] = $dubbizleId;
            $params['customer_id'] = $customerId;

            if ($vendor) {
                $params['user_type'] = 1;
            }

            if (! empty($user) || ! empty($contact) || ! empty($supplier) || ! empty($vendor) || ! empty($dubbizle) || ! empty($customer)) {
                // check that if message comes from customer,supplier,vendor
                if (! empty($customer)) {
                    $blockCustomer = BlockWebMessageList::where('object_id', $customer->id)->where('object_type', Customer::class)->first();
                    if ($blockCustomer) {
                        $blockCustomer->delete();
                    }
                }
                // check for vendor and remvove from the list
                if (! empty($vendor)) {
                    $blockVendor = BlockWebMessageList::where('object_id', $vendor->id)->where('object_type', Vendor::class)->first();
                    if ($blockVendor) {
                        $blockVendor->delete();
                    }
                }
                // check for supplier and remove from the list
                if (! empty($supplier)) {
                    $blockSupplier = BlockWebMessageList::where('object_id', $supplier->id)->where('object_type', Supplier::class)->first();
                    if ($blockSupplier) {
                        $blockSupplier->delete();
                    }
                }
                $message = ChatMessage::create($params);
            } else {
                // create a customer here
                $customer = Customer::create([
                    'name' => $from,
                    'phone' => $from,
                ]);
                $params['customer_id'] = $customer->id;
                $message = ChatMessage::create($params);
            }

            if ($customer != null) {
                ChatMessagesQuickData::updateOrCreate([
                    'model' => Customer::class,
                    'model_id' => $params['customer_id'],
                ], [
                    'last_unread_message' => @$params['message'],
                    'last_unread_message_at' => Carbon::now(),
                    'last_unread_message_id' => $message->id,
                ]);
            }

            // Is there a user linked to this number?
            if ($user) {
                // Add user ID to params

                // Check for task
                if ($params['message'] != '' && (preg_match_all("/#([\d]+)/i", $params['message'], $match))) {
                    // If task is found
                    if ($task = Task::find($match[1][0])) {
                        // Set the task_id parameter
                        $params['task_id'] = $match[1][0];

                        // Check for task users and set ERP user
                        if (count($task->users) > 0) {
                            if ($task->assign_from == $user->id) {
                                $params['erp_user'] = $task->assign_to;
                            } else {
                                $params['erp_user'] = $task->assign_from;
                            }
                        }

                        // Check for task contacts and set contact_id
                        if (count($task->contacts) > 0) {
                            if ($task->assign_from == $user->id) {
                                $params['contact_id'] = $task->assign_to;
                            } else {
                                $params['contact_id'] = $task->assign_from;
                            }
                        }
                    }
                }

                // Set media_url parameter
                if (isset($media)) {
                    $params['media_url'] = getMediaUrl($media);
                }

                // Attach media to message
                if (isset($media)) {
                    $message->attachMedia($media, config('constants.media_tags'));
                }

                // Send realtime message (???) if there is a task ID
                if (array_key_exists('task_id', $params) && ! empty($params['task_id'])) {
                    $this->sendRealTime($message, 'task_'.$task->id, $client);
                } else {
                    $this->sendRealTime($message, 'user_'.$user->id, $client);
                }
            }

            // Is there a contact linked to this number?
            if ($contact) {
                // Check for task ID
                if ($params['message'] != '' && (preg_match_all("/#([\d]+)/i", $params['message'], $match))) {
                    $params['task_id'] = $match[1][0];
                }

                if (array_key_exists('task_id', $params) && ! empty($params['task_id'])) {
                    $this->sendRealTime($message, 'task_'.$match[1][0], $client);
                } else {
                    $this->sendRealTime($message, 'user_'.$contact->id, $client);
                }
            }

            if ($supplier) {
                if ($params['media_url'] != null) {
                    self::saveProductFromSupplierIncomingImages($supplier->id, $params['media_url']);
                }
            }

            // Check for vendor
            if ($vendor) {
                // Set vendor category
                $category = $vendor->category;

                // Send message if all required data is set
                if ($category && $category->user_id && ($params['message'] || $params['media_url'])) {
                    $user = User::find($category->user_id);
                    if (isset($sendResult) && $sendResult) {
                        $message->unique_id = $sendResult['id'] ?? '';
                        $message->save();
                    }
                }

                $vendor->store_website_id = 1;
            }

            // check if the supplier message has been set then we need to send that message to erp user
            if ($supplier) {
                $phone = $supplier->phone;
                $whatsapp = $supplier->whatsapp_number;
                if (! $sendToSupplier) {
                    $phone = ChatMessage::getSupplierForwardTo();
                }

                $textMessage = ($sendToSupplier) ? $params['message'] : 'S-'.$supplier->id.'-('.$supplier->supplier.')=> '.$params['message'];
                if (isset($sendResult) && $sendResult) {
                    $message->unique_id = $sendResult['id'] ?? '';
                    $message->save();
                }
            }

            if ($dubbizle) {
                $model_type = 'dubbizle';
                $model_id = $dubbizle->id;

                $this->sendRealTime($message, 'dubbizle_'.$dubbizle->id, $client);

                $params['dubbizle_id'] = null;
            }

            if (($supplier || $vendor) && $message) {

                ChatbotReply::create([
                    'question' => $params['message'],
                    'replied_chat_id' => $message->id,
                    'reply_from' => 'database',
                ]);
            }

            // }

            // Get all numbers from config
            $config = WhatsappConfig::getWhatsappConfigs();

            // Set isCustomerNumber to false by default
            $isCustomerNumber = false;
            // Loop over instance IDs to check if the whatsapp number is used for incoming messages from customers
            foreach ($config as $whatsAppNumber => $arrNumber) {
                if ($arrNumber['instance_id'] == $instanceId) {
                    $to = $whatsAppNumber;
                    $isCustomerNumber = $arrNumber['customer_number'];
                    $instanceNumber = $whatsAppNumber;
                }
            }

            /// Also get all numbers from database
            if (! $isCustomerNumber && $customer != null) {
                $whatsappConfigs = WhatsappConfig::where('is_customer_support', 0)->get();

                // Loop over whatsapp configs
                if ($whatsappConfigs !== null) {
                    foreach ($whatsappConfigs as $whatsappConfig) {
                        if ($whatsappConfig->username == $instanceId) {
                            $isCustomerNumber = $whatsappConfig->number;
                            $instanceNumber = $whatsappConfig->number;
                        }
                    }
                }
            }

            // No to?
            if (empty($to)) {
                $to = $config[0]['number'];
            }

            WatsonChatJourney::updateOrCreate(['chat_message_id' => $quoted_message_id], ['reply_found_in_database' => 1]);
            WatsonChatJourney::updateOrCreate(['chat_message_id' => $quoted_message_id], ['reply' => $params['message']]);

            if ($customer) {
                (new SendImagesOfProduct)->check($message);
                MessageHelper::whatsAppSend($customer, $params['message'], true, $message, false, $parentMessage);
                WatsonChatJourney::updateOrCreate(['chat_message_id' => $quoted_message_id], ['response_sent_to_cusomer' => 1]);
            }

            // Is this message from a customer?
            $isCustomerNumber = true;
            if ($customer && $isCustomerNumber) {
                if ($params['message']) {
                    (new KeywordsChecker)->assignCustomerAndKeywordForNewMessage($params['message'], $customer);
                }

                if (isset($media)) {
                    if ($contentType === 'image') {
                        $message->attachMedia($media, $contentType);
                        $message->save();
                    }
                }

                $model_type = 'customers';
                $model_id = $customer->id;
                $customer->update([
                    'whatsapp_number' => $to,
                ]);

                $this->sendRealTime($message, 'customer_'.$customer->id, $client);

                if (Setting::get('forward_messages') == 1) {
                    if (Setting::get('forward_start_date') != null && Setting::get('forward_end_date') != null) {
                        $time = Carbon::now();
                        $start_date = Carbon::parse(Setting::get('forward_start_date'));
                        $end_date = Carbon::parse(Setting::get('forward_end_date'));

                        if ($time->between($start_date, $end_date, true)) {
                            $forward_users_ids = json_decode(Setting::get('forward_users'));
                            $second_message = '';

                            if ($message->message == null) {
                                $forwarded_message = "FORWARDED from $customer->name";
                                $second_message = $message->media_url;
                            } else {
                                $forwarded_message = "FORWARDED from $customer->name - ".$message->message;
                            }
                        }
                    } else {
                        $forward_users_ids = json_decode(Setting::get('forward_users'));
                        $second_message = '';

                        if ($message->message == null) {
                            $forwarded_message = "FORWARDED from $customer->name";
                            $second_message = $message->media_url;
                        } else {
                            $forwarded_message = "FORWARDED from $customer->name - ".$message->message;
                        }
                    }
                }

                // Auto DND
                if (array_key_exists('message', $params) && strtoupper($params['message']) == 'DND') {
                    if ($customer = Customer::find($params['customer_id'])) {
                        $customer->do_not_disturb = 1;
                        $customer->save();
                        Log::channel('customerDnd')->debug('(Customer ID '.$customer->id.' line '.$customer->name.' '.$customer->number.': Added To DND');

                        $dnd_params = [
                            'number' => null,
                            'user_id' => 6,
                            'approved' => 1,
                            'status' => 9,
                            'customer_id' => $customer->id,
                            'quoted_message_id' => $quoted_message_id,
                            'message' => AutoReply::where('type', 'auto-reply')->where('keyword', 'customer-dnd')->first()->reply,
                        ];
                        $auto_dnd_message = ChatMessage::create($dnd_params);
                    }
                }
                $data = [
                    'model' => $model_type,
                    'model_id' => $model_id,
                    'chat_message_id' => $params['unique_id'],
                    'message' => $message,
                    'status' => 'started',
                ];
                $chat_message_log_id = ChatbotMessageLog::generateLog($data);
                // Auto Instruction
                if ($params['customer_id'] != '1000' && $params['customer_id'] != '976') {
                    if ($customer = Customer::find($params['customer_id'])) {
                        ChatbotMessageLogResponse::StoreLogResponse([
                            'chatbot_message_log_id' => $chat_message_log_id,
                            'request' => '',
                            'response' => 'Price for customer send function started.',
                            'status' => 'success',
                        ]);
                        $params['chat_message_log_id'] = $chat_message_log_id;
                        MessageHelper::sendwatson($customer, $params['message'], true, $message, $params, false, 'customer');

                        WatsonChatJourney::updateOrCreate(['chat_message_id' => $quoted_message_id], ['reply_searched_in_watson' => 1]);
                        WatsonChatJourney::updateOrCreate(['chat_message_id' => $quoted_message_id], ['reply' => $params['message']]);

                        ChatbotMessageLogResponse::StoreLogResponse([
                            'chatbot_message_log_id' => $chat_message_log_id,
                            'request' => '',
                            'response' => 'Price for customer send function ended.',
                            'status' => 'success',
                        ]);
                    } else {
                        ChatbotMessageLogResponse::StoreLogResponse([
                            'chatbot_message_log_id' => $chat_message_log_id,
                            'request' => '',
                            'response' => 'Send watson function faield customer  ('.$params['customer_id'].')  not found.',
                            'status' => 'failed',
                        ]);
                    }
                }

                //Auto reply
                if (isset($customer->id) && $customer->id > 0) {
                    //
                }
            }
            // Moved to the bottom of this loop, since it overwrites the message
            $fromMe = $chatapiMessage['fromMe'] ?? true;
            $params['message'] = $originalMessage;
            if (! $fromMe && $params['message'] && strpos($originalMessage, 'V-') === 0) {
                $msg = $params['message'];
                $msg = explode(' ', $msg);
                $vendorData = $msg[0];
                $vendorId = trim(str_replace('V-', '', $vendorData));
                $message = str_replace('V-'.$vendorId, '', $params['message']);

                $vendor = Vendor::find($vendorId);
                if (! $vendor) {
                    return response('success');
                }

                $params['vendor_id'] = $vendorId;
                $params['customer_id'] = null;
                $params['approved'] = 1;
                $params['message'] = $message;
                $params['status'] = 2;

                ChatMessage::create($params);
            }

            if (! $fromMe && strpos($originalMessage, '#ISSUE-') === 0) {
                $m = new ChatMessage;
                $message = str_replace('#ISSUE-', '', $originalMessage);
                $m->issue_id = explode(' ', $message)[0];
                $m->user_id = isset($user->id) ? $user->id : null;
                $m->message = $originalMessage;
                $m->quoted_message_id = $quoted_message_id;
                $m->save();
            }

            if (! $fromMe && strpos($originalMessage, '#DEVTASK-') === 0) {
                $m = new ChatMessage;
                $message = str_replace('#DEVTASK-', '', $originalMessage);
                $m->developer_task_id = explode(' ', $message)[0];
                $m->user_id = isset($user->id) ? $user->id : null;
                $m->message = $originalMessage;
                $m->quoted_message_id = $quoted_message_id;
                $m->save();
            }
        }

        return response()->json('success', 200);
    }

    public function outgoingProcessed(Request $request): \Illuminate\Http\Response
    {
        $data = $request->json()->all();

        foreach ($data as $event) {
            $chat_message = ChatMessage::find($event->data->reference);

            if ($chat_message) {
                $chat_message->sent = 1;
                $chat_message->save();
            }
        }

        return response('success');
    }

    public function getAllMessages(Request $request): JsonResponse
    {
        $params = [];
        $result = [];
        if ($request->customerId) {
            $column = 'customer_id';
            $value = $request->customerId;
        } else {
            if ($request->supplierId) {
                $column = 'supplier_id';
                $value = $request->supplierId;
            } else {
                if ($request->taskId) {
                    $column = 'task_id';
                    $value = $request->taskId;
                } else {
                    if ($request->erpUser) {
                        $column = 'erp_user';
                        $value = $request->erpUser;
                    } else {
                        if ($request->dubbizleId) {
                            $column = 'dubbizle_id';
                            $value = $request->dubbizleId;
                        } else {
                            $column = 'customer_id';
                            $value = $request->customerId;
                        }
                    }
                }
            }
        }

        //     $messages = DB::select('
        //               SELECT chat_messages.id, chat_messages.customer_id, chat_messages.number, chat_messages.user_id, chat_messages.erp_user, chat_messages.assigned_to, chat_messages.approved, chat_messages.status, chat_messages.sent, chat_messages.error_status, chat_messages.resent, chat_messages.created_at, chat_messages.media_url, chat_messages.message,
        //               media.filename,
        //               mediable_id

        //               FROM chat_messages

        //               LEFT JOIN (
        //                 SELECT * FROM media
        //                 SELECT * FROM media

        //                 RIGHT JOIN
        //                 (SELECT * FROM mediables WHERE mediable_type LIKE "%ChatMessage%") as mediables
        //                 ON mediables.media_id = media.id
        //               ) AS media

        //               ON mediable_id = chat_messages.id

        //               WHERE ' . $column . ' = ' . $value . ' AND status != 7
        //               ORDER BY chat_messages.created_at DESC
        //   ');

        $messages = ChatMessage::select(
            'chat_messages.id',
            'chat_messages.customer_id',
            'chat_messages.number',
            'chat_messages.user_id',
            'chat_messages.erp_user',
            'chat_messages.assigned_to',
            'chat_messages.approved',
            'chat_messages.status',
            'chat_messages.sent',
            'chat_messages.error_status',
            'chat_messages.resent',
            'chat_messages.created_at',
            'chat_messages.media_url',
            'chat_messages.message',
            'media.filename',
            'mediables.mediable_id'
        )
            ->leftJoin('media', function ($join) {
                $join->on('mediables.media_id', '=', 'media.id')
                    ->where('mediables.mediable_type', 'LIKE', '%ChatMessage%');
            })
            ->leftJoin('mediables', 'mediables.mediable_id', '=', 'chat_messages.id')
            ->where($column, $value)
            ->where('status', '!=', 7)
            ->orderByDesc('chat_messages.created_at')
            ->get();

        if (Setting::get('show_automated_messages') == 0) {
            $messages = $messages->where('status', '!=', 9);
        }

        if ($request->erpUser) {
            $messages = $messages->whereNull('task_id');
        }

        // IS IT NECESSARY ?
        if ($request->get('elapse')) {
            $elapse = (int) $request->get('elapse');
            $date = new \DateTime;
            $date->modify(sprintf('-%s seconds', $elapse));
        }

        foreach ($messages->latest()->get() as $message) {
            $messageParams = [
                'id' => $message->id,
                'number' => $message->number,
                'assigned_to' => $message->assigned_to,
                'created_at' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
                'approved' => $message->approved,
                'status' => $message->status,
                'user_id' => $message->user_id,
                'erp_user' => $message->erp_user,
                'sent' => $message->sent,
                'resent' => $message->resent,
                'error_status' => $message->error_status,
            ];

            if ($message->media_url) {
                $messageParams['media_url'] = $message->media_url;
                $headers = get_headers($message->media_url, 1);
                $messageParams['content_type'] = $headers['Content-Type'][1];
            }

            if ($message->message) {
                $messageParams['message'] = $message->message;
            }

            if ($message->hasMedia(config('constants.media_tags'))) {
                $images_array = [];

                foreach ($message->getMedia(config('constants.media_tags')) as $key => $image) {
                    $temp_image = [
                        'key' => $image->getKey(),
                        'image' => getMediaUrl($image),
                        'product_id' => '',
                        'special_price' => '',
                        'size' => '',
                    ];

                    $image_key = $image->getKey();
                    $mediable_type = 'Product';

                    $product_image = Product::with('Media')
                        ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key AND mediables.mediable_type LIKE '%$mediable_type%')")
                        ->select(['id', 'price_inr_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

                    if ($product_image) {
                        $temp_image['product_id'] = $product_image->id;
                        $temp_image['special_price'] = $product_image->price_inr_special;

                        $string = $product_image->supplier;
                        $expr = '/(?<=\s|^)[a-z]/i';
                        preg_match_all($expr, $string, $matches);
                        $supplier_initials = implode('', $matches[0]);
                        $temp_image['supplier_initials'] = strtoupper($supplier_initials);

                        if ($product_image->size != null) {
                            $temp_image['size'] = $product_image->size;
                        } else {
                            $temp_image['size'] = (string) $product_image->lmeasurement.', '.(string) $product_image->hmeasurement.', '.(string) $product_image->dmeasurement;
                        }
                    }

                    array_push($images_array, $temp_image);
                }

                $messageParams['images'] = $images_array;
            }

            $result[] = array_merge($params, $messageParams);
        }

        $result = array_values(collect($result)->sortBy('created_at')->reverse()->toArray());
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;

        if ($request->page) {
            $currentItems = array_slice($result, $perPage * ($currentPage - 1), $perPage);
        } else {
            $currentItems = array_reverse(array_slice($result, $perPage * ($currentPage - 1), $perPage));
            $result = array_reverse($result);
        }

        $result = new LengthAwarePaginator($currentItems, count($result), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return response()->json($result);
    }

    public function logchatmessage($log_case_id, $task_id, $message, $log_msg)
    {
        $log = new LogChatMessage;
        $log->log_case_id = $log_case_id;
        $log->task_id = $task_id;
        $log->message = $message;
        $log->log_msg = $log_msg;
        $log->save();
    }

    /**
     * Send message
     *
     *
     * @param  mixed  $context
     * @param  mixed  $ajaxNeeded
     * @return \Illuminate\Http\Response
     *
     * @throws \Plank\Mediable\Exceptions\MediaUrlException
     */
    public function sendMessage(SendMessageWhatsAppRequest $request, $context, $ajaxNeeded = false)
    {

        $data = $request->except('_token');
        $chat_id = 0;
        if (isset($data['chat_id'])) {
            $chat_id = $data['chat_id'];
        }

        // set if there is no queue defaut for all pages
        if (! isset($data['is_queue'])) {
            $data['is_queue'] = 0;
        }
        $data['user_id'] = ((int) $request->get('user_id', 0) > 0) ? (int) $request->get('user_id', 0) : Auth::id();
        $data['number'] = $request->get('number');

        $loggedUser = $request->user();
        try {
            if ($request->add_autocomplete == 'true') {
                $exist = AutoCompleteMessage::where('message', $request->message)->exists();
                if (! $exist) {
                    AutoCompleteMessage::create([
                        'message' => $request->message,
                    ]);
                }
            }
            if ($context == 'email') {
                $lastMessage = ChatMessage::find($request->chat_id);
                $data['from_email'] = $lastMessage->from_email;
                $data['to_email'] = $lastMessage->to_email;

                $data['is_email'] = 1;
                $data['email_id'] = $request->email_id;
                $data['message_type'] = 'email';
                unset($data['user_id']);
                $module_id = $request->email_id;
            } elseif ($context == 'customer') {
                $data['customer_id'] = $request->customer_id;
                $module_id = $request->customer_id;
                //update if the customer message is going to send then update all old message to read
                ChatMessage::updatedUnreadMessage($request->customer_id, $data['status']);
                $this->logchatmessage('#1', $request->task_id, $request->message, 'if the customer message is going to send');
                // update message for chatbot request->customer_id
                if (! empty($data['status']) && ! in_array($data['status'], ChatMessage::AUTO_REPLY_CHAT)) {

                    ChatMessage::join('chatbot_replies as cr', 'cr.replied_chat_id', '=', 'chat_messages.id')->where('chat_messages.customer_id', $request->customer_id)->where('cr.is_read', 0)->update(['cr.is_read' => 1]);
                    ChatMessage::join('chatbot_replies as cr', 'cr.chat_id', '=', 'chat_messages.id')->where('chat_messages.customer_id', $request->customer_id)->where('cr.is_read', 0)->update(['cr.is_read' => 1]);

                }
            } elseif ($context == 'purchase') {
                $data['purchase_id'] = $request->purchase_id;
                $module_id = $request->purchase_id;
                $this->logchatmessage('#2', $request->task_id, $request->message, 'if the purchase message is going to send');
            } elseif ($context == 'supplier') {
                $data['supplier_id'] = $request->supplier_id;
                $module_id = $request->supplier_id;
                $this->logchatmessage('#3', $request->task_id, $request->message, 'if the supplier message is going to send');
            } elseif ($context == 'SOP-Data') {
                $data['sop_user_id'] = $request->sop_user_id;
                $module_id = $request->sop_user_id;
                $this->logchatmessage('#4', $request->task_id, $request->message, 'if the supplier message is going to send');
            } elseif ($context == 'chatbot') { //Purpose : Add Chatbotreplay - DEVTASK-18280
                $data['customer_id'] = $request->customer_id;
                $module_id = $request->customer_id;
                ChatMessage::updatedUnreadMessage($request->customer_id, $data['status']);
                $this->logchatmessage('#5', $request->task_id, $request->message, 'if the chatbot message is going to send');
            } elseif ($context == 'user-feedback') {
                $data['user_feedback_id'] = $request->user_id;
                $data['user_feedback_category_id'] = $request->feedback_cat_id;
                $data['user_feedback_status'] = $request->feedback_status_id;
                $Admin_users = User::get();
                foreach ($Admin_users as $u) {
                    if ($u->isAdmin()) {
                        $u_id = $u->id;
                        break;
                    }
                }
                if (Auth::user()->isAdmin()) {
                    $u_id = $request->user_id;
                }
                $data['user_id'] = $u_id;
                $data['sent_to_user_id'] = $u_id;
                $data['send_by'] = Auth::user()->isAdmin() ? Auth::id() : null;
                $module_id = $u_id;
                $this->logchatmessage('#6', $request->task_id, $request->message, 'if the user-feedback message is going to send');
            } elseif ($context == 'user-feedback-hrTicket') {
                $data['user_feedback_id'] = $request->user_id;
                $data['user_feedback_category_id'] = $request->feedback_cat_id;
                $data['user_feedback_status'] = $request->feedback_status_id;
                $Admin_users = User::get();
                foreach ($Admin_users as $u) {
                    if ($u->isAdmin()) {
                        $u_id = $u->id;
                        break;
                    }
                }
                if (Auth::user()->isAdmin()) {
                    $u_id = $request->user_id;
                }
                $data['user_id'] = $u_id;
                $data['sent_to_user_id'] = $u_id;
                $data['send_by'] = Auth::user()->isAdmin() ? Auth::id() : null;
                $module_id = $u_id;
                $this->logchatmessage('#20', $request->task_id, $request->message, 'if the user-feedback HR Ticket message is going to send');
            } elseif ($context == 'hubstuff') {
                $data['hubstuff_activity_user_id'] = $request->hubstuff_id;
                $module_id = $request->hubstuff_id;
                $this->logchatmessage('#7', $request->task_id, $request->message, 'if the hubstuff message is going to send');
            } elseif ($context == 'timedoctor') {
                $data['time_doctor_activity_user_id'] = $request->time_doctor_id;
                $module_id = $request->time_doctor_id;
                $this->logchatmessage('#7', $request->task_id, $request->message, 'if the time doctor message is going to send');
            } else {
                if ($context == 'vendor') {
                    $data['vendor_id'] = $request->vendor_id;
                    $module_id = $request->vendor_id;
                    if ($request->get('is_vendor_user') == 'yes') {
                        $user = User::find($request->get('vendor_id'));
                        $vendor = Vendor::where('phone', $user->phone)->first();
                        $data['vendor_id'] = $vendor->id;
                        $module_id = $vendor->id;
                    }
                    if ($request->get('message')) {
                        $data['message'] = $request->get('message');
                    }

                    // update message for chatbot request->vendor_id

                    if (! empty($data['status']) && ! in_array($data['status'], ChatMessage::AUTO_REPLY_CHAT)) {
                        ChatMessage::join('chatbot_replies as cr', 'cr.replied_chat_id', '=', 'chat_messages.id')->where('chat_messages.vendor_id', $request->vendor_id)->where('cr.is_read', 0)->update(['cr.is_read' => 1]);
                        ChatMessage::join('chatbot_replies as cr', 'cr.chat_id', '=', 'chat_messages.id')->where('chat_messages.vendor_id', $request->vendor_id)->where('cr.is_read', 0)->update(['cr.is_read' => 1]);
                    }
                    $this->logchatmessage('#8', $request->task_id, $request->message, 'update message for chatbot request');
                } elseif ($context == 'charity') {
                    $data['charity_id'] = $request->vendor_id;
                    $charity = CustomerCharity::where('id', $request->vendor_id)->first();
                    $module_id = $request->vendor_id;
                    if ($request->get('message')) {
                        $data['message'] = $request->get('message');
                    }

                    // update message for chatbot request->vendor_id

                    if (! empty($data['status']) && ! in_array($data['status'], ChatMessage::AUTO_REPLY_CHAT)) {
                        ChatMessage::join('chatbot_replies as cr', 'cr.replied_chat_id', '=', 'chat_messages.id')->where('chat_messages.charity_id', $request->charity_id)->where('cr.is_read', 0)->update(['cr.is_read' => 1]);
                        ChatMessage::join('chatbot_replies as cr', 'cr.chat_id', '=', 'chat_messages.id')->where('chat_messages.charity_id', $request->charity_id)->where('cr.is_read', 0)->update(['cr.is_read' => 1]);

                    }
                    $this->logchatmessage('#9', $request->task_id, $request->message, 'update message for charity request');
                    unset($data['vendor_id']);
                } elseif ($context == 'uicheckMessage') {
                    $this->logchatmessage('#21', $request->task_id, $request->message, 'If context UI check');
                    $message = $request->get('message');
                    $params['message'] = $message;
                    $params['message_en'] = $request->get('message');
                    $params['ticket_id'] = $request->ticket_id;
                    $params['user_id'] = $request->object_id;
                    $params['ui_check_id'] = $request->task_id;
                    $params['approved'] = 1;
                    $params['status'] = 2;
                    $chat_message = ChatMessage::create($params);

                    return response()->json(['message' => $chat_message]);
                } elseif ($context == 'task') {
                    if ($request->task_id == 'undefined') {
                        $this->logchatmessage('#10', null, $request->message, 'If context conndition task is exit');
                    } else {
                        $this->logchatmessage('#10', $request->task_id, $request->message, 'If context conndition task is exit');
                    }
                    $data['task_id'] = $request->task_id;
                    $data['is_audio'] = $request->get('is_audio', 0);
                    $task = Task::find($request->task_id);

                    if ($task->is_statutory != 1) {
                        $data['message'] = '#'.$data['task_id'].'. '.$task->task_subject.'. '.$data['message'];
                    } else {
                        $data['message'] = $task->task_subject.'. '.$data['message'];
                    }

                    if ($request->send_message_recepients) {
                        $recepients = explode(',', $request->send_message_recepients);
                        foreach ($recepients as $recepient) {
                            if ($recepient == 'assign_by') {
                                $adm = User::find($task->assign_from);
                                if ($adm) {
                                    WebNotificationController::sendBulkNotification($adm->id, 'Task & Activity', $data['message']);

                                    if ($adm->slack_id) {
                                        $SlackNotificationService = new SlackNotificationService;
                                        $SlackNotificationService->sendMessage($adm->slack_id, $data['message']);
                                    }
                                }
                            } elseif ($recepient == 'assigned_to') {
                                foreach ($task->users as $key => $user) {
                                    WebNotificationController::sendBulkNotification($user->id, 'Task & Activity', $data['message']);
                                }
                            } elseif ($recepient == 'master_user_id') {
                                if (! empty($task->master_user_id)) {
                                    $userMaster = User::find($task->master_user_id);
                                    if ($userMaster) {
                                        WebNotificationController::sendBulkNotification($userMaster->id, 'Task & Activity', $data['message']);
                                    }
                                }
                            } elseif ($recepient == 'second_master_user_id') {
                                if (! empty($task->second_master_user_id)) {
                                    $userMaster = User::find($task->second_master_user_id);
                                    if ($userMaster) {
                                        //$this->sendWithThirdApi($userMaster->phone, $userMaster->whatsapp_number, $data['message']);
                                        WebNotificationController::sendBulkNotification($userMaster->id, 'Task & Activity', $data['message']);
                                    }
                                }
                            } elseif ($recepient == 'contacts') {
                                if (count($task->contacts) > 0) {
                                    foreach ($task->contacts as $key => $contact) {
                                        if ($key == 0) {
                                            $data['contact_id'] = $task->assign_to;
                                        } else {
                                            //
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if (count($task->users) > 0) {
                            if ($task->assign_from == Auth::id()) {
                                foreach ($task->users as $key => $user) {
                                    if ($key == 0) {
                                        $data['erp_user'] = $user->id;
                                    } else {
                                        WebNotificationController::sendBulkNotification($user->id, 'Task & Activity', $data['message']);
                                    }
                                }
                            } elseif ($task->master_user_id == Auth::id()) {
                                foreach ($task->users as $key => $user) {
                                    if ($key == 0) {
                                        $data['erp_user'] = $user->id;
                                    } else {
                                        WebNotificationController::sendBulkNotification($user->id, 'Task & Activity', $data['message']);
                                    }
                                }
                                $adm = User::find($task->assign_from);
                                if ($adm) {
                                    WebNotificationController::sendBulkNotification($adm->id, 'Task & Activity', $data['message']);
                                }
                            } else {
                                if (! $task->users->contains(Auth::id())) {
                                    $data['erp_user'] = $task->assign_from;

                                    foreach ($task->users as $key => $user) {
                                        WebNotificationController::sendBulkNotification($user->id, 'Task & Activity', $data['message']);
                                    }
                                } else {
                                    foreach ($task->users as $key => $user) {
                                        if ($key == 0) {
                                            $data['erp_user'] = $task->assign_from;
                                        } else {
                                            if ($user->id != Auth::id()) {
                                                WebNotificationController::sendBulkNotification($user->id, 'Task & Activity', $data['message']);
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (count($task->contacts) > 0) {
                            foreach ($task->contacts as $key => $contact) {
                                if ($key == 0) {
                                    $data['contact_id'] = $task->assign_to;
                                } else {
                                    //
                                }
                            }
                        }

                        // this will send message to the lead developer
                        if (! empty($task->master_user_id)) {
                            $userMaster = User::find($task->master_user_id);
                            if ($userMaster) {
                                $extraUser = $data;
                                $extraUser['erp_user'] = $task->master_user_id;
                                $extraUser['user_id'] = $task->master_user_id;
                            }
                        }
                    }
                    $params['approved'] = 1;
                    $params['status'] = 2;
                    $chat_message = ChatMessage::create($data);
                    $this->logchatmessage('#11', $request->task_id, $request->message, 'New chat message is created');
                    $module_id = $request->task_id;

                    /** Sent To ChatbotMessage */
                    $loggedUser = auth()->user();
                    $roles = ($loggedUser) ? $loggedUser->roles->pluck('name')->toArray() : [];

                    if (! in_array('Admin', $roles)) {
                        ChatbotReply::create([
                            'question' => '#'.$task->id.' => '.$request->message,
                            'reply' => json_encode([
                                'context' => 'task',
                                'issue_id' => $task->id,
                                'from' => ($loggedUser) ? $loggedUser->id : 'cron',
                            ]),
                            'replied_chat_id' => $chat_message->id,
                            'reply_from' => 'database',
                        ]);
                    }

                    // update message for chatbot request->vendor_id

                    if (! empty($data['status']) && ! in_array($data['status'], ChatMessage::AUTO_REPLY_CHAT)) {
                        ChatMessage::join('chatbot_replies as cr', 'cr.replied_chat_id', '=', 'chat_messages.id')->where('chat_messages.task_id', $task->id)->where('cr.is_read', 0)->update(['cr.is_read' => 1]);
                        ChatMessage::join('chatbot_replies as cr', 'cr.chat_id', '=', 'chat_messages.id')->where('chat_messages.task_id', $task->id)->where('cr.is_read', 0)->update(['cr.is_read' => 1]);
                    }

                    $message_ = '[ '.@$loggedUser->name.' ] - #'.$task->id.' - '.$task->task_subject."\n\n".$request->message;

                    MessageHelper::sendEmailOrWebhookNotification($task->users->pluck('id')->toArray(), $message_);
                } elseif ($context == 'learning') {
                    $this->logchatmessage('#12', $request->task_id, $request->message, 'If context learning is exit');
                    $learning = Learning::find($request->issue_id);
                    if ($data['user_id'] == $learning->learning_vendor) {
                        $userId = $data['user_id'];
                    } else {
                        $userId = $learning->learning_vendor;
                    }

                    $prefix = null;
                    if ($learning && $learning->learningUser) {
                        $prefix = '#'.$learning->id.' '.$learning->learningUser->name.' : '.$learning->learning_subject.' =>';
                    }

                    $params['message'] = $prefix.$request->get('message');
                    $params['erp_user'] = $userId;
                    $params['sent_to_user_id'] = $userId;
                    $params['learning_id'] = $request->issue_id; //Purpose - Add learning_id - DEVTASK-4020
                    $params['user_id'] = $userId;
                    $params['approved'] = 1;
                    $params['status'] = 2;
                    $number = User::find($userId);
                    if (! $number) {
                        return response()->json(['message' => null]);
                    }
                    $whatsapp = $number->whatsapp_number;
                    $number = $number->phone;

                    $chat_message = ChatMessage::create($params);

                    return response()->json(['message' => $chat_message]);
                } elseif ($context == 'ticket') {
                    $this->logchatmessage('#13', $request->task_id, $request->message, 'If context ticket is exit');
                    $send_ticket_options = [];
                    if (isset($request->send_ticket_options)) {
                        $send_ticket_options = explode(',', $request->send_ticket_options);
                    }
                    $data['ticket_id'] = $request->ticket_id;
                    $module_id = $request->ticket_id;
                    $ticket = Tickets::find($request->ticket_id);
                    $message = $request->get('message');
                    $messageScore = null;
                    if ($ticket) {
                        if ($ticket->lang_code != '' && $ticket->lang_code != 'en') {
                            $message = TranslationHelper::translate('en', $ticket->lang_code, $message);
                            $messageScore = app('translation-lambda-helper')->getTranslateScore($request->get('message'), $message);
                        }
                    }
                    if ($messageScore) {
                        $params['message_score'] = $messageScore;
                    }
                    $params['message'] = $message;
                    $params['message_en'] = $request->get('message');
                    $params['ticket_id'] = $request->ticket_id;
                    $params['customer_id'] = $ticket->customer_id;
                    $params['approved'] = 1;
                    $params['status'] = 2;
                    $params['user_id'] = auth()->user()?->id;
                    $chat_message = ChatMessage::create($params);

                    // check if ticket has customer ?
                    $whatsappNo = null;
                    if ($ticket->user) {
                        $whatsappNo = $ticket->user->whatsapp_number;
                    } elseif ($ticket->customer) {
                        $whatsappNo = $ticket->customer->whatsapp_number;
                    }
                    foreach ($send_ticket_options as $send_ticket_option) {
                        if ($send_ticket_option == 'whatsapp') {
                            //
                        } elseif ($send_ticket_option == 'send_to_tickets') {
                            $chat_message->send_to_tickets = 1;
                            $chat_message->save();
                        }
                    }
                    Log::info('Start API CALL /rest/V1/ticket-counter/add');
                    if ($ticket) {
                        $ticket_id = $ticket->ticket_id;
                        $email = $ticket->email;
                        $source_of_ticket = $ticket->source_of_ticket;
                        $storeWebsite = StoreWebsite::where('website', $source_of_ticket)->first();
                        if ($storeWebsite) {
                            $storeWebsiteCode = $storeWebsite->storeCode;
                            $magento_url = $storeWebsite->magento_url;
                            $api_token = $storeWebsite->api_token;
                            if (! empty($magento_url) && ! empty($storeWebsiteCode)) {
                                $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                                $curl = curl_init();
                                $url = trim($magento_url, '/')."/{$storeWebsiteCode->code}/rest/V1/ticket-counter/add";
                                curl_setopt_array($curl, [
                                    CURLOPT_URL => trim($magento_url, '/')."/{$storeWebsiteCode->code}/rest/V1/ticket-counter/add",
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'PUT',
                                    CURLOPT_POSTFIELDS => '{
                                "ticketData":
                                {
                                    "ticket_id": "'.$ticket_id.'",
                                    "email": "'.$email.'"
                                }
                                }
                                ',
                                    CURLOPT_HTTPHEADER => [
                                        'Content-Type: application/json',
                                        'Authorization: Bearer '.$api_token,
                                    ],
                                ]);

                                $response = curl_exec($curl);
                                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                                Log::info('API RESPONSE: '.$response);
                                curl_close($curl);

                                LogRequest::log($startTime, $url, 'PUT', '{
                                "ticketData":
                                {
                                    "ticket_id": "'.$ticket_id.'",
                                    "email": "'.$email.'"
                                }
                                }', json_decode($response), $httpcode, WhatsAppController::class, 'sendMessage');
                            } else {
                                Log::info('Magento URL and Store Website code is not found:');
                            }
                        } else {
                            Log::info('Store Website not found: '.$source_of_ticket);
                        }
                    } else {
                        Log::info('Ticket Data Not Found ticket_id: '.$request->ticket_id);
                    }
                    Log::info('END API CALL /rest/V1/ticket-counter/add');

                    return response()->json(['message' => $chat_message]);
                } else {
                    if ($context == 'priority') {
                        $this->logchatmessage('#14', $request->task_id, $request->message, 'If context priority is exit');
                        $params = [];
                        $params['message'] = $request->get('message', '');
                        $params['erp_user'] = $request->get('user_id', 0);
                        $params['user_id'] = $request->get('user_id', 0);
                        $params['approved'] = 1;
                        $params['status'] = 2;

                        $number = User::find($request->get('user_id', 0));

                        if (! $number) {
                            return response()->json(['message' => null]);
                        }
                        $whatsapp_number = $number->whatsapp_number;
                        $number = $number->phone;

                        $chat_message = ChatMessage::create($params);

                        return response()->json(['message' => $chat_message]);
                    } elseif ($context == 'activity') {
                        $this->logchatmessage('#15', $request->task_id, $request->message, 'If context activity is exit');
                        $data['erp_user'] = $request->user_id;
                        $module_id = $request->user_id;
                        $user = User::find($request->user_id);
                        if ($user && $user->phone) {
                            //
                        }
                    } elseif ($context == 'overdue') {
                        $this->logchatmessage('#16', $request->task_id, $request->message, 'If context overdue is exit');
                        $data['erp_user'] = $request->user_id;
                        $user = User::find($request->user_id);
                    } elseif ($context == 'user') {
                        $this->logchatmessage('#17', $request->task_id, $request->message, 'If context user is exit');
                        $data['erp_user'] = $request->user_id;
                        $module_id = $request->user_id;
                        $user = User::find($request->user_id);
                    } elseif ($context == 'dubbizle') {
                        $this->logchatmessage('#18', $request->task_id, $request->message, 'If context dubbizle is exit');
                        $data['dubbizle_id'] = $request->dubbizle_id;
                        $module_id = $request->dubbizle_id;
                    } elseif ($context == 'time_approval') {
                        $this->logchatmessage('#19', $request->task_id, $request->message, 'If context dubbizle is exit');
                        $summary = HubstaffActivitySummary::find($request->summery_id);
                        if ($summary) {
                            $userId = $summary->user_id;
                            $number = User::find($userId);
                            if (! $number) {
                                return response()->json(['message' => null]);
                            }
                            $whatsapp = $number->whatsapp_number;
                            $number = $number->phone;

                            $params['erp_user'] = $userId;
                            $params['user_id'] = $userId;
                            $params['sent_to_user_id'] = $userId;
                            $params['approved'] = 1;
                            $params['status'] = 2;
                            $params['hubstaff_activity_summary_id'] = $request->summery_id;
                            $params['message'] = $request->message;
                            $chat_message = ChatMessage::create($params);

                            return response()->json(['message' => $chat_message]);
                        }

                        return response()->json(['message' => null]);
                    } elseif ($context == 'issue') {
                        $sendTo = $request->get('sendTo', 'to_developer');
                        $params['issue_id'] = $request->get('issue_id');
                        $params['is_audio'] = $request->get('is_audio', 0);
                        $issue = DeveloperTask::find($request->get('issue_id'));

                        $userId = $issue->assigned_to;

                        $users = User::find($userId);

                        if ($users && $users->slack_id) {
                            $SlackNotificationService = new SlackNotificationService;
                            $SlackNotificationService->sendMessage($users->slack_id, $data['message']);
                        }

                        if ($sendTo == 'to_master') {
                            if ($issue->master_user_id) {
                                $userId = $issue->master_user_id;
                            }
                        }

                        if ($sendTo == 'to_team_lead') {
                            if ($issue->team_lead_id) {
                                $userId = $issue->team_lead_id;
                            }
                        }

                        if ($sendTo == 'to_tester') {
                            if ($issue->tester_id) {
                                $userId = $issue->tester_id;
                            }
                        }
                        $admin = 0;
                        if (! Auth::user() || ! Auth::user()->isAdmin()) {
                            $admin = $issue->created_by;
                        }
                        $params['erp_user'] = $userId;
                        $params['user_id'] = $data['user_id'];
                        $params['sent_to_user_id'] = $userId;
                        $params['approved'] = 1;
                        $params['status'] = 2;

                        $number = User::find($userId);
                        if (! $number) {
                            return response()->json(['message' => null]);
                        }
                        $whatsapp = $number->whatsapp_number;
                        $number = $number->phone;
                        if ($request->type == 1) {
                            foreach ($issue->getMedia(config('constants.media_tags')) as $image) {
                                $params['message'] = '#TASK-'.$issue->id.'-'.$issue->subject.'=>'.getMediaUrl($image);
                                $params['media_url'] = getMediaUrl($image);

                                if (Auth::user()->id != $userId) {
                                    $chat_message = ChatMessage::create($params);
                                }
                                if ($admin) {
                                    $creator = User::find($admin);
                                    if ($creator) {
                                        $num = $creator->phone;
                                        $whatsapp = $creator->whatsapp_number;
                                        $params['erp_user'] = $admin;
                                        $params['user_id'] = $data['user_id'];
                                        $params['sent_to_user_id'] = $admin;
                                        $params['approved'] = 1;
                                        $params['status'] = 2;
                                        $chat_message = ChatMessage::create($params);
                                    }
                                }
                            }
                        } elseif ($request->type == 2) {
                            $issue = Issue::find($request->get('issue_id'));
                            if ($request->hasfile('images')) {
                                foreach ($request->file('images') as $image) {
                                    $media = MediaUploader::fromSource($image)->upload();
                                    $issue->attachMedia($media, config('constants.media_tags'));
                                    $params['message'] = '#ISSUE-'.$issue->id.'-'.$issue->subject.'=>'.getMediaUrl($media);
                                    $params['media_url'] = getMediaUrl($media);
                                    if (Auth::user()->id != $userId) {
                                        $chat_message = ChatMessage::create($params);
                                    }

                                    if ($admin) {
                                        $creator = User::find($admin);
                                        if ($creator) {
                                            $num = $creator->phone;
                                            $whatsapp = $creator->whatsapp_number;
                                            $params['erp_user'] = $admin;
                                            $params['user_id'] = $data['user_id'];
                                            $params['sent_to_user_id'] = $admin;
                                            $params['approved'] = 1;
                                            $params['status'] = 2;
                                            $chat_message = ChatMessage::create($params);
                                        }
                                    }
                                }
                            }
                        } else {
                            $params['developer_task_id'] = $request->get('issue_id');
                            $prefix = ($issue->task_type_id == 1) ? '#DEVTASK-' : '#ISSUE-';
                            $params['message'] = $prefix.$issue->id.'-'.$issue->subject.'=>'.$request->get('message');
                            if (Auth::user() && Auth::user()->id != $userId) {
                                $chat_message = ChatMessage::create($params);
                            }
                            if ($admin) {
                                $creator = User::find($admin);
                                if ($creator) {
                                    $num = $creator->phone;
                                    $whatsapp = $creator->whatsapp_number;
                                    $params['erp_user'] = $admin;
                                    $params['user_id'] = $data['user_id'];
                                    $params['sent_to_user_id'] = $admin;
                                    $params['approved'] = 1;
                                    $params['status'] = 2;
                                    $chat_message = ChatMessage::create($params);
                                }
                            }

                            if ($issue->hasMedia(config('constants.media_tags'))) {
                                foreach ($issue->getMedia(config('constants.media_tags')) as $image) {
                                    $params['media_url'] = getMediaUrl($image);
                                    if (Auth::user()->id != $userId) {
                                        $chat_message = ChatMessage::create($params);
                                    }
                                    if ($admin) {
                                        $creator = User::find($admin);
                                        if ($creator) {
                                            $num = $creator->phone;
                                            $whatsapp = $creator->whatsapp_number;
                                            $params['erp_user'] = $admin;
                                            $params['user_id'] = $data['user_id'];
                                            $params['sent_to_user_id'] = $admin;
                                            $params['approved'] = 1;
                                            $params['status'] = 2;
                                            $chat_message = ChatMessage::create($params);
                                        }
                                    }
                                }
                            }
                        }

                        ChatMessagesQuickData::updateOrCreate([
                            'model' => DeveloperTask::class,
                            'model_id' => $params['issue_id'],
                        ], [
                            'last_communicated_message' => @$params['message'],
                            'last_communicated_message_at' => Carbon::now(),
                            'last_communicated_message_id' => isset($chat_message) ? $chat_message->id : null,
                        ]);

                        // update message for chatbot request->vendor_id

                        if (! empty($data['status']) && ! in_array($data['status'], ChatMessage::AUTO_REPLY_CHAT)) {
                            ChatMessage::join('chatbot_replies as cr', 'cr.replied_chat_id', '=', 'chat_messages.id')->where('chat_messages.developer_task_id', $issue->id)->where('cr.is_read', 0)->update(['cr.is_read' => 1]);
                            ChatMessage::join('chatbot_replies as cr', 'cr.chat_id', '=', 'chat_messages.id')->where('chat_messages.developer_task_id', $issue->id)->where('cr.is_read', 0)->update(['cr.is_read' => 1]);
                        }

                        if ($sendTo == 'to_master') {
                            /* Send to chatbot/messages */

                            ChatbotReply::create([
                                'question' => '#DEVTASK-'.$issue->id.' => '.$request->message,
                                'reply' => json_encode([
                                    'context' => 'issue',
                                    'issue_id' => $issue->id,
                                    'from' => $request->user()->id,
                                ]),
                                'replied_chat_id' => isset($chat_message) ? $chat_message->id : '',
                                'reply_from' => 'database',
                            ]);
                        }

                        if ($request->chat_reply_message_id) {
                            $messageReply = ChatbotReply::find($request->chat_reply_message_id);

                            if ($messageReply) {
                                $prefix = ($issue->task_type_id == 1) ? '#DEVTASK-' : '#ISSUE-';

                                $messageReply->chat_id = $chat_message->id;

                                $messageReply->save();
                            }
                        }

                        //START - Purpose : Email notification - DEVTASK-4359
                        $user = User::find($issue->assigned_to);

                        $message_ = ($issue->task_type_id == 1 ? '[ '.$user->name.' ] - #DEVTASK-' : '#ISSUE-').$issue->id.' - '.$issue->subject."\n\n".$request->message;

                        MessageHelper::sendEmailOrWebhookNotification([$issue->assigned_to, $issue->team_lead_id, $issue->tester_id], $message_);
                        //END - DEVTASK-4359
                        WebNotificationController::sendWebNotification2($request->get('sendTo'), $params['issue_id'], $prefix.$issue->id.'-'.$issue->subject, $request->get('message'));

                        return response()->json(['message' => isset($chat_message) ? $chat_message : '']);
                    } elseif ($context == 'auto_task') {
                        $params['issue_id'] = $request->get('issue_id');
                        $issue = DeveloperTask::find($request->get('issue_id'));
                        $userId = $issue->assigned_to;

                        $admin = $issue->created_by;

                        $params['erp_user'] = $userId;
                        $params['user_id'] = $data['user_id'];
                        $params['sent_to_user_id'] = $userId;
                        $params['approved'] = 1;
                        $params['status'] = 2;

                        $number = User::find($userId);
                        if (! $number) {
                            return response()->json(['message' => null]);
                        }
                        $whatsapp = $number->whatsapp_number;
                        $number = $number->phone;
                        $params['developer_task_id'] = $request->get('issue_id');
                        $prefix = ($issue->task_type_id == 1) ? '#DEVTASK-' : '#ISSUE-';
                        $params['message'] = $prefix.$issue->id.'-'.$issue->subject.'=>'.$request->get('message');
                        $chat_message = ChatMessage::create($params);

                        if ($admin) {
                            $creator = User::find($admin);
                            if ($creator) {
                                $num = $creator->phone;
                                $whatsapp = $creator->whatsapp_number;
                                $params['erp_user'] = $admin;
                                $params['user_id'] = $data['user_id'];
                                $params['sent_to_user_id'] = $admin;
                                $params['approved'] = 1;
                                $params['status'] = 2;
                                $chat_message = ChatMessage::create($params);
                            }
                        }
                        ChatMessagesQuickData::updateOrCreate([
                            'model' => DeveloperTask::class,
                            'model_id' => $params['issue_id'],
                        ], [
                            'last_communicated_message' => @$params['message'],
                            'last_communicated_message_at' => Carbon::now(),
                            'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
                        ]);

                        $message_ = ($issue->task_type_id == 1 ? '[ '.$loggedUser->name.' ]- #DEVTASK-' : '#ISSUE-').$issue->id.' - '.$issue->subject."\n\n".$request->message;

                        $this->sendEmailOrWebhookNotification([$userId], $message_);

                        return response()->json(['message' => $chat_message]);
                    } elseif ($context == 'document') {
                        //Sending Documents To User / Vendor / Contacts
                        $data['document_id'] = $request->document_id;
                        $module_id = $request->document_id;

                        //Getting User For Sending Documents
                        if ($request->user_type == 1) {
                            $document = Document::findOrFail($module_id);
                            $document_url = $document->getDocumentPathById($document->id);

                            foreach ($request->users as $key) {
                                $user = User::findOrFail($key);

                                // User ID For Chat Message
                                $data['user_id'] = $user->id;

                                //Creating Chat Message
                                $chat_message = ChatMessage::create($data);

                                //History
                                $history['send_by'] = Auth::id();
                                $history['send_to'] = $user->id;
                                $history['type'] = 'User';
                                $history['via'] = 'WhatsApp';
                                $history['document_id'] = $document->id;
                                DocumentSendHistory::create($history);
                            }

                            //Getting Vendor For Sending Documents
                        } elseif ($request->user_type == 2) {
                            $document = Document::findOrFail($module_id);
                            $document_url = $document->getDocumentPathById($document->id);
                            foreach ($request->users as $key) {
                                $vendor = Vendor::findOrFail($key);

                                // Vendor ID For Chat Message
                                $data['vendor_id'] = $vendor->id;

                                //Creating Chat Message
                                $chat_message = ChatMessage::create($data);

                                //History
                                $history['send_by'] = Auth::id();
                                $history['send_to'] = $vendor->id;
                                $history['type'] = 'Vendor';
                                $history['via'] = 'WhatsApp';
                                $history['document_id'] = $document->id;
                                DocumentSendHistory::create($history);
                            }

                            //Getting Contact For Sending Documents
                        } elseif ($request->user_type == 3) {
                            $document = Document::findOrFail($module_id);
                            $document_url = $document->getDocumentPathById($document->id);
                            foreach ($request->users as $key) {
                                $contact = Contact::findOrFail($key);

                                // Contact ID For Chat Message
                                $data['contact_id'] = $contact->id;

                                //Creating Chat Message
                                $chat_message = ChatMessage::create($data);

                                //History
                                $history['send_by'] = Auth::id();
                                $history['send_to'] = $contact->id;
                                $history['type'] = 'Contact';
                                $history['via'] = 'WhatsApp';
                                $history['document_id'] = $document->id;
                                DocumentSendHistory::create($history);
                            }
                        } elseif (isset($request->contact) && $request->contact != null) {
                            $document = Document::findOrFail($module_id);
                            $document_url = $document->getDocumentPathById($document->id);

                            foreach ($request->contact as $contacts) {
                                // Contact ID For Chat Message
                                $data['number'] = $contacts;

                                //Creating Chat Message
                                $chat_message = ChatMessage::create($data);

                                //History
                                $history['send_by'] = Auth::id();
                                $history['send_to'] = $contacts;
                                $history['type'] = 'Manual Contact';
                                $history['via'] = 'WhatsApp';
                                $history['document_id'] = $document->id;
                                DocumentSendHistory::create($history);
                            }
                        }

                        return redirect()->back()->with('message', 'Document Send SucessFully');
                    } elseif ($context == 'quicksell') {
                        $product = Product::findorfail($request->quicksell_id);
                        $image = $product->getMedia(config('constants.media_tags'))->first()
                            ? getMediaUrl($product->getMedia(config('constants.media_tags'))->first())
                            : '';
                        foreach ($request->customers as $key) {
                            $customer = Customer::findOrFail($key);

                            // User ID For Chat Message
                            $data['customer_id'] = $customer->id;

                            //Creating Chat Message
                            $chat_message = ChatMessage::create($data);
                        }

                        return redirect()->back()->with('message', 'Images Send SucessFully');
                    } elseif ($context == 'quicksell_group') {
                        $products = $request->products;
                        if ($products != null) {
                            $products = explode(',', $products);
                            foreach ($products as $product) {
                                $product = Product::findorfail($product);
                                $image = $product->getMedia(config('constants.media_tags'))->first()
                                    ? getMediaUrl($product->getMedia(config('constants.media_tags'))->first())
                                    : '';
                                if (isset($request->to_all)) {
                                    $customers = Customer::all();
                                } elseif (! empty($request->customers_id) && is_array($request->customers_id)) {
                                    $customers = Customer::whereIn('id', $request->customers_id)->get();
                                } elseif ($request->customers != null) {
                                    $customers = Customer::whereIn('id', $request->customers)->get();
                                } elseif ($request->rating != null && $request->gender == null) {
                                    $customers = Customer::where('rating', $request->rating)->get();
                                } elseif ($request->rating != null && $request->gender != null) {
                                    $customers = Customer::where('rating', $request->rating)->where('gender', $request->gender)->get();
                                } else {
                                    return redirect()->route('quicksell.index')->with('message', 'Please select Category');
                                }

                                if ($customers != null) {
                                    foreach ($customers as $customer) {
                                        $data['customer_id'] = $customer->id;
                                        $chat_message = ChatMessage::create($data);
                                    }
                                }
                            }
                        } else {
                            if (! empty($request->redirect_back)) {
                                return redirect()->to($request->redirect_back)->with('message', 'Please Select Products');
                            }

                            return redirect()->route('quicksell.index')->with('message', 'Please Select Products');
                        }

                        if ($request->redirect_back) {
                            return redirect()->to($request->redirect_back)->with('message', 'Images Send SucessFully');
                        }

                        return redirect()->route('quicksell.index')->with('message', 'Images Send SucessFully');
                    } elseif ($context == 'quicksell_group_send') {
                        if ($request->customerId != null && $request->groupId != null) {
                            //Find Group id
                            foreach ($request->groupId as $id) {
                                //got group
                                $groups = QuickSellGroup::select('id', 'group')->where('id', $id)->get();

                                //getting product id from group
                                if ($groups != null) {
                                    foreach ($groups as $group) {
                                        $medias = [];

                                        $products = Product::with('media')
                                            ->select('products.*')
                                            ->join('product_quicksell_groups', 'product_quicksell_groups.product_id', '=', 'products.id')
                                            ->groupBy('products.id')
                                            ->where('quicksell_group_id', $group->group)
                                            ->get();

                                        foreach ($products as $product) {
                                            $image = $product->media()->whereIn('tag', config('constants.attach_image_tag'))->first();
                                            if ($image) {
                                                array_push($medias, $image);
                                            }
                                        }

                                        if (isset($medias) && count($medias) > 0) {
                                            if (! empty($request->send_pdf) && $request->send_pdf == 1) {
                                                $fn = '';
                                                if ($context == 'customer') {
                                                    $fn = '_product';
                                                }

                                                $folder = 'temppdf_view_'.time();

                                                $pdfView = view('pdf_views.images'.$fn, compact('medias', 'folder'));
                                                $pdf = new Dompdf;
                                                $pdf->setPaper([0, 0, 1000, 1000], 'portrait');
                                                $pdf->loadHtml($pdfView);
                                                if (! empty($request->pdf_file_name)) {
                                                    $random = str_replace(' ', '-', $request->pdf_file_name.'-'.date('Y-m-d-H-i-s-').rand());
                                                } else {
                                                    $random = uniqid('sololuxury_', true);
                                                }
                                                if (! File::isDirectory(public_path().'/pdf/')) {
                                                    File::makeDirectory(public_path().'/pdf/', 0777, true, true);
                                                }
                                                $fileName = public_path().'/pdf/'.$random.'.pdf';
                                                $pdf->render();

                                                File::put($fileName, $pdf->output());

                                                $media = MediaUploader::fromSource($fileName)->upload();

                                                if ($request->customerId != null) {
                                                    $customer = Customer::findorfail($request->customerId);
                                                    if (! empty($request->send_pdf)) {
                                                        $file = config('env.APP_URL').'/pdf/'.$random.'.pdf';
                                                    }
                                                    $data['customer_id'] = $customer->id;
                                                    $chat_message = ChatMessage::create($data);
                                                }
                                            } else {
                                                if ($medias != null) {
                                                    if ($request->customerId != null) {
                                                        $customer = Customer::findorfail($request->customerId);
                                                        foreach ($medias as $media) {
                                                            $file = getMediaUrl($media);
                                                            $data['customer_id'] = $customer->id;
                                                            $chat_message = ChatMessage::create($data);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            return response()->json(['success']);
                        }
                    } elseif ($context == 'old') {
                        $old = Old::findorfail($request->old_id);

                        if ($old != null) {
                            $data['old_id'] = $old->serial_no;
                            //Creating Chat Message
                            $data['message'] = $request->message;
                            $chat_message = ChatMessage::create($data);

                            return response()->json([
                                'data' => $data,
                            ], 200);
                        }
                    } elseif ($context == 'site_development') {
                        $chat_message = null;
                        $users = $request->get('users', [$request->get('user_id', 6)]);
                        if (! empty($users)) {
                            foreach ($users as $user) {
                                $user = User::find($user);
                                if ($user) {
                                    $params['message'] = $request->get('message');
                                    $params['site_development_id'] = $request->get('site_development_id');
                                    $params['approved'] = 1;
                                    $params['status'] = 2;
                                    $chat_message = ChatMessage::create($params);

                                    return response()->json(['message' => $chat_message], 200);
                                }
                            }
                        }

                        return response()->json(['message' => 'No user selected'], 500);
                    } elseif ($context == 'content_management') {
                        $chat_message = null;
                        $users = $request->get('users', [$request->get('user_id', 0)]);

                        if (! empty($users)) {
                            foreach ($users as $user) {
                                $user = User::find($user);
                                $params['message'] = $request->get('message');
                                $params['store_social_content_id'] = $request->get('store_social_content_id');
                                $params['approved'] = 1;
                                $params['status'] = 2;
                                $chat_message = ChatMessage::create($params);
                            }
                        }

                        return response()->json(['message' => $chat_message]);
                    } elseif ($context == 'task_lead') {
                        $params['task_id'] = $request->get('task_id');
                        $params['message'] = $request->get('message');

                        $params['approved'] = 1;
                        $params['status'] = 2;
                        $task = Task::find($request->get('task_id'));
                        $user = User::find($task->master_user_id);

                        if (! $user) {
                            return response()->json(['message' => 'Master user not found'], 500);
                        }
                        $params['user_id'] = $user->id;
                        if ($task->is_statutory != 1) {
                            $params['message'] = '#'.$task->id.'. '.$task->task_subject.'. '.$params['message'];
                        } else {
                            $params['message'] = $task->task_subject.'. '.$params['message'];
                        }

                        $number = $user->phone;
                        $whatsapp_number = $user->whatsapp_number;
                        if (! $number) {
                            return response()->json(['message' => 'User whatsapp no not available'], 500);
                        }
                        $chat_message = ChatMessage::create($params);
                    } elseif ($context == 'social_strategy') {
                        $user = User::find($request->get('user_id'));

                        $params['message'] = $request->get('message');

                        $params['social_strategy_id'] = $request->get('social_strategy_id');
                        $params['approved'] = 1;

                        $params['status'] = 2;

                        $chat_message = ChatMessage::create($params);

                        return response()->json(['message' => $chat_message]);
                    } elseif ($context == 'payment-receipts') {
                        $user = null;
                        $paymentReceipt = PaymentReceipt::find($request->get('payment_receipt_id'));
                        if ($paymentReceipt) {
                            if (auth()->user()->isAdmin()) {
                                $user = User::find($paymentReceipt->user_id);
                            }
                        }
                        if (! $user) {
                            $user = User::find(6);
                        }

                        $params['erp_user'] = $user->id;
                        $params['user_id'] = $user->id;
                        $params['message'] = $request->get('message');
                        $params['payment_receipt_id'] = $request->get('payment_receipt_id');
                        $params['approved'] = 1;
                        $params['status'] = 2;

                        $chat_message = ChatMessage::create($params);

                        return response()->json(['message' => $chat_message]);
                    } else {
                        if ($context == 'developer_task') {
                            $params['developer_task_id'] = $request->get('developer_task_id');
                            $task = DeveloperTask::find($request->get('developer_task_id'));
                            $params['erp_user'] = $task->user_id;
                            $params['approved'] = 1;
                            $params['message'] = '#DEVTASK-'.$task->id.' '.$request->get('message');
                            $params['status'] = 2;

                            $user = User::find($task->user_id);
                            $number = $user->phone;
                            $whatsapp_number = $user->whatsapp_number;
                            $chat_message = ChatMessage::create($params);

                            return response()->json(['message' => $chat_message]);
                        } else {
                            if ($context == 'lawyer') {
                                $data['lawyer_id'] = $request->lawyer_id;
                                $module_id = $request->lawyer_id;
                            } else {
                                if ($context == 'case') {
                                    $data['case_id'] = $request->case_id;
                                    $data['lawyer_id'] = $request->lawyer_id;
                                    $module_id = $request->case_id;
                                } else {
                                    if ($context == 'blogger') {
                                        $data['blogger_id'] = $request->blogger_id;
                                        $module_id = $request->blogger_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ($context != 'task') {
                $params['approved'] = 0;
                $params['status'] = 1;
                $chat_message = ChatMessage::create($data);
            }

            //START - Purpose : Add ChatbotMessage entry - DEVTASK-4203
            if ($context == 'vendor') {
                /** Sent To ChatbotMessage */
                $loggedUser = $request->user();

                if ($loggedUser) {
                    $roles = $loggedUser->roles->pluck('name')->toArray();

                    if (! in_array('Admin', $roles)) {
                        ChatbotReply::create([
                            'question' => $request->message,
                            'reply' => json_encode([
                                'context' => 'vendor',
                                'issue_id' => $data['vendor_id'],
                                'from' => $loggedUser->id,
                            ]),
                            'replied_chat_id' => $chat_message->id,
                            'reply_from' => 'database',
                        ]);
                    }

                    $messageReply = ChatbotReply::find($request->chat_reply_message_id);

                    if ($messageReply) {
                        $messageReply->chat_id = $chat_message->id;

                        $messageReply->save();
                    }
                }
            }
            //END - DEVTASK-4203

            //STRAT - Purpose : Add record in chatbotreplay - DEVTASK-18280
            if ($context == 'chatbot') {
                if ($request->chat_reply_message_id) {
                    $messageReply = ChatbotReply::find($request->chat_reply_message_id);

                    if ($messageReply) {
                        $messageReply->chat_id = $chat_message->id;

                        $messageReply->save();
                    }
                }

                return response()->json(['message' => $chat_message]);
            }
            //END - DEVTASK-18280

            if ($context == 'customer') {
                ChatMessagesQuickData::updateOrCreate([
                    'model' => Customer::class,
                    'model_id' => $data['customer_id'],
                ], [
                    'last_communicated_message' => @$data['message'],
                    'last_communicated_message_at' => Carbon::now(),
                    'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
                ]);
            }

            if ($context == 'task') {
                ChatMessagesQuickData::updateOrCreate([
                    'model' => Task::class,
                    'model_id' => $data['task_id'],
                ], [
                    'last_communicated_message' => @$data['message'],
                    'last_communicated_message_at' => Carbon::now(),
                    'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
                ]);

                if ($request->chat_reply_message_id) {
                    $messageReply = ChatbotReply::find($request->chat_reply_message_id);

                    if ($messageReply) {
                        $messageReply->chat_id = $chat_message->id;

                        $messageReply->save();
                    }
                }
            }

            if ($context == 'task_lead') {
                ChatMessagesQuickData::updateOrCreate([
                    'model' => Task::class,
                    'model_id' => $data['task_id'],
                ], [
                    'last_communicated_message' => @$params['message'],
                    'last_communicated_message_at' => Carbon::now(),
                    'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
                ]);
            }

            if ($context == 'email') {
                ChatMessagesQuickData::updateOrCreate([
                    'model' => Email::class,
                    'model_id' => $data['email_id'],
                ], [
                    'last_communicated_message' => @$params['message'],
                    'last_communicated_message_at' => Carbon::now(),
                    'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
                ]);
            }

            if ($request->hasFile('image')) {
                $media = MediaUploader::fromSource($request->file('image'))
                    ->toDirectory('chatmessage/'.floor($chat_message->id / config('constants.image_per_folder')))
                    ->upload();
                $chat_message->attachMedia($media, config('constants.media_tags'));

                if ($context == 'task') {
                    if (count($task->users) > 0) {
                        if ($task->assign_from == Auth::id()) {
                            foreach ($task->users as $key => $user) {
                                if ($key == 0) {
                                    $data['erp_user'] = $user->id;
                                } else {
                                    //
                                }
                            }
                        } else {
                            foreach ($task->users as $key => $user) {
                                if ($key == 0) {
                                    $data['erp_user'] = $task->assign_from;
                                } else {
                                    if ($user->id != Auth::id()) {
                                        //
                                    }
                                }
                            }
                        }
                    }

                    if (count($task->contacts) > 0) {
                        foreach ($task->contacts as $key => $contact) {
                            if ($key == 0) {
                                $data['contact_id'] = $task->assign_to;
                            } else {
                                //
                            }
                        }
                    }
                }
            }

            // get the status for approval
            $approveMessage = DevelopmentHelper::needToApproveMessage();
            $isNeedToBeSend = false;
            // if (
            //     (int) $approveMessage == 1
            //         || (Auth::id() == 49 && empty($chat_message->customer_id))
            //         || Auth::id() == 56
            //         || Auth::id() == 3
            //         || Auth::id() == 65
            //         || $context == 'task'
            //         || $request->get('is_vendor_user') == 'yes'

            // ) {
            //     $isNeedToBeSend = true;
            // }

            $isNeedToBeSend = true;

            if ($request->images) {
                $imagesDecoded = json_decode($request->images, true);
                if (! empty($request->send_pdf) && $request->send_pdf == 1) {
                    $fn = ($context == 'customer' || $context == 'customers') ? '_product' : '';
                    $folder = 'temppdf_view_'.time();
                    $mediasH = Media::whereIn('id', $imagesDecoded)->get();
                    $number = 0;
                    $chunkedMedia = $mediasH->chunk(self::MEDIA_PDF_CHUNKS);

                    foreach ($chunkedMedia as $key => $medias) {
                        $pdfView = (string) view('pdf_views.images'.$fn, compact('medias', 'folder', 'chat_message'));
                        $pdf = new Dompdf;
                        $pdf->setPaper([0, 0, 1000, 1000], 'portrait');
                        $pdf->loadHtml($pdfView);

                        if (! empty($request->pdf_file_name)) {
                            $random = str_replace(' ', '-', $request->pdf_file_name.'-'.($key + 1).'-'.date('Y-m-d-H-i-s-').rand());
                        } else {
                            $random = uniqid('sololuxury_', true);
                        }

                        $fileName = public_path().'/'.$random.'.pdf';
                        $pdf->render();

                        File::put($fileName, $pdf->output());

                        // send images in chunks to chat media
                        try {
                            if ($number == 0) {
                                $media = MediaUploader::fromSource($fileName)
                                    ->toDirectory('chatmessage/'.floor($chat_message->id / config('constants.image_per_folder')))
                                    ->upload();
                                $chat_message->attachMedia($media, config('constants.media_tags'));
                            } else {
                                $extradata = $data;
                                $extradata['is_queue'] = 0;
                                $extra_chat_message = ChatMessage::create($extradata);
                                $media = MediaUploader::fromSource($fileName)
                                    ->toDirectory('chatmessage/'.floor($extra_chat_message->id / config('constants.image_per_folder')))
                                    ->upload();
                                $extra_chat_message->attachMedia($media, config('constants.media_tags'));
                            }

                            File::delete($fileName);

                            $number++;
                        } catch (Exception $e) {
                            Log::channel('whatsapp')->error($e);
                        }
                    }
                } else {
                    if (! empty($imagesDecoded) && is_array($imagesDecoded)) {
                        if ($request->type == 'customer-attach') {
                            foreach ($imagesDecoded as $iimg => $listedImage) {
                                $productList = SuggestedProductList::find($listedImage);
                                $product = Product::find($productList->product_id);
                                $imageDetails = $product->getMedia(config('constants.attach_image_tag'))->first();
                                $image_key = $imageDetails->getKey();
                                $media = Media::find($image_key);
                                if ($media) {
                                    $mediable = Mediables::where('media_id', $media->id)->where('mediable_type', Product::class)->first();
                                    try {
                                        if ($iimg != 0) {
                                            $chat_message = ChatMessage::create($data);
                                        }
                                        $chat_message->attachMedia($media, config('constants.media_tags'));
                                        if ($mediable) {
                                            $productList->update(['chat_message_id' => $chat_message->id]);
                                        }
                                        // if this message is not first then send to the client
                                        if ($iimg != 0 && $isNeedToBeSend && $chat_message->status != 0 && $chat_message->is_queue == '0') {
                                            $myRequest = new Request;
                                            $myRequest->setMethod('POST');
                                            $myRequest->request->add(['messageId' => $chat_message->id]);
                                            $this->approveMessage($context, $myRequest);
                                            if ($mediable) {
                                                $productList->update(['chat_message_id' => $chat_message->id]);
                                            }
                                        }
                                    } catch (Exception $e) {
                                        Log::channel('whatsapp')->error($e);
                                    }
                                }
                            }
                        } else {
                            $medias = Media::whereIn('id', array_unique($imagesDecoded))->get();
                            if (! $medias->isEmpty()) {
                                foreach ($medias as $iimg => $media) {
                                    $mediable = Mediables::where('media_id', $media->id)->where('mediable_type', Product::class)->first();
                                    try {
                                        if ($iimg != 0) {
                                            $chat_message = ChatMessage::create($data);
                                        }
                                        $chat_message->attachMedia($media, config('constants.media_tags'));
                                        // if this message is not first then send to the client
                                        if ($iimg != 0 && $isNeedToBeSend && $chat_message->status != 0 && $chat_message->is_queue == '0') {
                                            $myRequest = new Request;
                                            $myRequest->setMethod('POST');
                                            $myRequest->request->add(['messageId' => $chat_message->id]);
                                            $this->approveMessage($context, $myRequest);
                                        }
                                    } catch (Exception $e) {
                                        Log::channel('whatsapp')->error($e);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($request->screenshot_path != '') {
                $image_path = public_path().'/uploads/temp_screenshot.png';
                $img = substr($request->screenshot_path, strpos($request->screenshot_path, ',') + 1);
                $img = Image::make(base64_decode($img))->encode('png')->save($image_path);

                $media = MediaUploader::fromSource($image_path)
                    ->toDirectory('chatmessage/'.floor($chat_message->id / config('constants.image_per_folder')))
                    ->upload();
                $chat_message->attachMedia($media, config('constants.media_tags'));

                File::delete('uploads/temp_screenshot.png');
            }

            // get the status for approval
            if ($isNeedToBeSend && $chat_message->status != 0 && $chat_message->is_queue == '0') {
                $myRequest = new Request;
                $myRequest->setMethod('POST');
                $myRequest->request->add(['messageId' => $chat_message->id]);
                $this->approveMessage($context, $myRequest, $chat_id);
            }

            if ($request->ajax() || $ajaxNeeded) {
                return response()->json(['message' => $chat_message]);
            }

            return redirect()->to('/'.$context.'/'.$module_id);
        } catch (Exception $e) {
            return response()->json(['message' => 'Opps! Something went wrong, Please try again.'], 400);
        }
    }

    public function sendMultipleMessages(Request $request)
    {
        $selected_leads = json_decode($request->selected_leads, true);
        $leads = ErpLeads::whereIn('id', $selected_leads)->get();

        if (count($leads) > 0) {
            foreach ($leads as $lead) {
                try {
                    $params = [];
                    $model_type = 'leads';
                    $model_id = $lead->id;
                    $params = [
                        'lead_id' => $lead->id,
                        'number' => null,
                        'message' => $request->message,
                        'user_id' => Auth::id(),
                    ];

                    if ($lead->customer) {
                        $params['customer_id'] = $lead->customer->id;
                    }

                    $message = ChatMessage::create($params);
                } catch (Exception $ex) {
                    return response($ex->getMessage(), 500);
                }
            }
        }

        return redirect()->route('leads.index');
    }

    public function updateAndCreate(Request $request)
    {

        $message = Message::find($request->message_id);
        $params = [
            'number' => null,
            'status' => 1,
            'user_id' => Auth::id(),
        ];

        if ($message) {
            $params = [
                'approved' => 1,
                'status' => 2,
                'created_at' => Carbon::now(),
            ];

            if ($request->moduletype == 'leads') {
                $params['lead_id'] = $message->moduleid;
                if ($lead = ErpLeads::find($message->moduleid)) {
                    if ($lead->customer) {
                        $params['customer_id'] = $lead->customer->id;
                    }
                }
            } elseif ($request->moduletype == 'orders') {
                $params['order_id'] = $message->moduleid;
                if ($order = Order::find($message->moduleid)) {
                    if ($order->customer) {
                        $params['customer_id'] = $order->customer->id;
                    }
                }
            } elseif ($request->moduletype == 'customer') {
                $customer = Customer::find($message->customer_id);
                $params['customer_id'] = $customer->id;
            } elseif ($request->moduletype == 'purchase') {
                $params['purchase_id'] = $message->moduleid;
            }

            $images = $message->getMedia(config('constants.media_tags'));

            if ($images->first()) {
                $params['message'] = null;
                $chat_message = ChatMessage::create($params);

                foreach ($images as $img) {
                    $chat_message->attachMedia($img, config('constants.media_tags'));
                }
            } else {
                $params['message'] = $message->body;

                $chat_message = ChatMessage::create($params);
            }

            $myRequest = new Request;
            $myRequest->setMethod('POST');
            $myRequest->request->add(['messageId' => $chat_message->id]);

            $result = $this->approveMessage($request->moduletype, $myRequest);
        } else {
            if ($request->moduletype == 'customer') {
                $params['customer_id'] = $request->moduleid;
                $params['order_id'] = null;
            } elseif ($request->moduletype == 'leads') {
                $params['lead_id'] = $request->moduleid;
                if ($lead = ErpLeads::find($request->moduleid)) {
                    if ($lead->customer) {
                        $params['customer_id'] = $lead->customer->id;
                    }
                }
            } else {
                $params['order_id'] = $request->moduleid;
                if ($order = Order::find($request->moduleid)) {
                    if ($order->customer) {
                        $params['customer_id'] = $order->customer->id;
                    }
                }
            }

            if ($request->images) {
                $params['message'] = null;
                $chat_message = ChatMessage::create($params);
                foreach (json_decode($request->images) as $image) {
                    $media = Media::find($image);
                    $chat_message->attachMedia($media, config('constants.media_tags'));
                }
            }

            return redirect('/'.(! empty($request->moduletype) ? $request->moduletype : 'customer').'/'.$request->moduleid);
        }

        return response()->json(['status' => $result]);
    }

    public function forwardMessage(Request $request): RedirectResponse
    {
        $message = ChatMessage::find($request->message_id);

        foreach ($request->customer_id as $customer_id) {
            $new_message = new ChatMessage;
            $new_message->number = $message->number;
            $new_message->message = $message->message;
            $new_message->lead_id = $message->lead_id;
            $new_message->order_id = $message->order_id;
            $new_message->user_id = $message->user_id;
            $new_message->customer_id = $customer_id;
            $new_message->status = 1;
            $new_message->media_url = $message->media_url;

            $new_message->save();

            if ($images = $message->getMedia(config('constants.media_tags'))) {
                foreach ($images as $image) {
                    $new_message->attachMedia($image, config('constants.media_tags'));
                }
            }
        }

        return redirect()->back();
    }

    /**
     * poll messages
     *
     * @param  mixed  $context
     */
    public function pollMessages(Request $request, $context): JsonResponse
    {
        $params = [];
        $result = [];
        $skip = $request->page && $request->page > 1 ? $request->page * 10 : 0;

        $column = 'customer_id'; // Default values
        $column_value = $request->customerId;

        if ($context === 'purchase') {
            $column = 'purchase_id';
            $column_value = $request->purchaeId;
        }

        $messages = ChatMessage::select(['id', "$column", 'number', 'user_id', 'assigned_to', 'approved', 'status', 'sent', 'resent', 'created_at', 'media_url', 'message'])->where($column, $column_value)->latest();

        // IS IT NECESSARY ?
        if ($request->get('elapse')) {
            $elapse = (int) $request->get('elapse');
            $date = new \DateTime;
            $date->modify(sprintf('-%s seconds', $elapse));
        }

        foreach ($messages->get() as $message) {
            $messageParams = [
                'id' => $message->id,
                'number' => $message->number,
                'assigned_to' => $message->assigned_to,
                'created_at' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
                'approved' => $message->approved,
                'status' => $message->status,
                'user_id' => $message->user_id,
                'sent' => $message->sent,
                'resent' => $message->resent,
            ];

            if ($message->media_url) {
                $messageParams['media_url'] = $message->media_url;
                $headers = get_headers($message->media_url, 1);
                $messageParams['content_type'] = $headers['Content-Type'][1];
            }

            if ($message->message) {
                $messageParams['message'] = $message->message;
            }

            if ($message->hasMedia(config('constants.media_tags'))) {
                $images_array = [];

                foreach ($message->getMedia(config('constants.media_tags')) as $key => $image) {
                    $temp_image = [
                        'key' => $image->getKey(),
                        'image' => getMediaUrl($image),
                        'product_id' => '',
                        'special_price' => '',
                        'size' => '',
                    ];

                    $image_key = $image->getKey();

                    $product_image = Product::with('Media')
                        ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key)")
                        ->select(['id', 'price_inr_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

                    if ($product_image) {
                        $temp_image['product_id'] = $product_image->id;
                        $temp_image['special_price'] = $product_image->price_inr_special;

                        $string = $product_image->supplier;
                        $expr = '/(?<=\s|^)[a-z]/i';
                        preg_match_all($expr, $string, $matches);
                        $supplier_initials = implode('', $matches[0]);
                        $temp_image['supplier_initials'] = strtoupper($supplier_initials);

                        if ($product_image->size != null) {
                            $temp_image['size'] = $product_image->size;
                        } else {
                            $temp_image['size'] = (string) $product_image->lmeasurement.', '.(string) $product_image->hmeasurement.', '.(string) $product_image->dmeasurement;
                        }
                    }

                    array_push($images_array, $temp_image);
                }

                $messageParams['images'] = $images_array;
            }

            $result[] = array_merge($params, $messageParams);
        }

        $result = array_values(collect($result)->sortBy('created_at')->reverse()->toArray());
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;

        if ($request->page) {
            $currentItems = array_slice($result, $perPage * ($currentPage - 1), $perPage);
        } else {
            $currentItems = array_reverse(array_slice($result, $perPage * ($currentPage - 1), $perPage));
        }

        $result = new LengthAwarePaginator($currentItems, count($result), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return response()->json($result);
    }

    public function pollMessagesCustomer(Request $request): JsonResponse
    {
        // Remove time limit
        set_time_limit(0);

        $params = [];
        $result = [];
        if ($request->customerId) {
            $column = 'customer_id';
            $value = $request->customerId;
        } else {
            if ($request->supplierId) {
                $column = 'supplier_id';
                $value = $request->supplierId;
            } else {
                if ($request->vendorId) {
                    $column = 'vendor_id';
                    $value = $request->vendorId;
                } else {
                    if ($request->taskId) {
                        $column = 'task_id';
                        $value = $request->taskId;
                    } else {
                        if ($request->erpUser) {
                            $column = 'erp_user';
                            $value = $request->erpUser;
                        } else {
                            if ($request->dubbizleId) {
                                $column = 'dubbizle_id';
                                $value = $request->dubbizleId;
                            } else {
                                if ($request->lawyerId) {
                                    $column = 'lawyer_id';
                                    $value = $request->lawyerId;
                                } else {
                                    if ($request->caseId) {
                                        $column = 'case_id';
                                        $value = $request->caseId;
                                    } else {
                                        if ($request->bloggerId) {
                                            $column = 'blogger_id';
                                            $value = $request->bloggerId;
                                        } else {
                                            if ($request->customerId) {
                                                $column = 'customer_id';
                                                $value = $request->customerId;
                                            } else {
                                                if ($request->oldID) {
                                                    $column = 'old_id';
                                                    $value = $request->oldId;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $select_fields = ['id', 'customer_id', 'number', 'user_id', 'erp_user', 'assigned_to', 'approved', 'status', 'sent', 'error_status', 'resent', 'created_at', 'media_url', 'message'];
        if ($request->caseId) {
            array_push($select_fields, 'lawyer_id');
        }
        $messages = ChatMessage::select($select_fields)->where($column, $value)->where('status', '!=', 7);

        if ($request->caseId) {
            $messages = $messages->with('lawyer:id,name');
        }

        if (Setting::get('show_automated_messages') == 0) {
            $messages = $messages->where('status', '!=', 9);
        }

        if ($request->erpUser) {
            $messages = $messages->whereNull('task_id');
        }

        // IS IT NECESSARY ?
        if ($request->get('elapse')) {
            $elapse = (int) $request->get('elapse');
            $date = new \DateTime;
            $date->modify(sprintf('-%s seconds', $elapse));
        }

        foreach ($messages->latest()->get() as $message) {
            $messageParams = [
                'id' => $message->id,
                'number' => $message->number,
                'assigned_to' => $message->assigned_to,
                'created_at' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
                'approved' => $message->approved,
                'status' => $message->status,
                'user_id' => $message->user_id,
                'erp_user' => $message->erp_user,
                'sent' => $message->sent,
                'resent' => $message->resent,
                'error_status' => $message->error_status,
            ];
            if ($request->caseId) {
                $messageParams['lawyer'] = $message->lawyer?->name;
            }

            if ($message->media_url) {
                $messageParams['media_url'] = $message->media_url;
                $headers = get_headers($message->media_url, 1);
                $messageParams['content_type'] = $headers['Content-Type'][1];
            }

            if ($message->message) {
                $messageParams['message'] = $message->message;
            }

            if ($message->hasMedia(config('constants.media_tags'))) {
                $images_array = [];

                foreach ($message->getMedia(config('constants.media_tags')) as $key => $image) {
                    $temp_image = [
                        'key' => $image->getKey(),
                        'image' => getMediaUrl($image),
                        'product_id' => '',
                        'special_price' => '',
                        'size' => '',
                    ];

                    $image_key = $image->getKey();
                    $mediable_type = 'Product';

                    $product_image = Product::with('Media')
                        ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key AND mediables.mediable_type LIKE '%$mediable_type%')")
                        ->select(['id', 'price_inr_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

                    if ($product_image) {
                        $temp_image['product_id'] = $product_image->id;
                        $temp_image['special_price'] = $product_image->price_inr_special;

                        $string = $product_image->supplier;
                        $expr = '/(?<=\s|^)[a-z]/i';
                        preg_match_all($expr, $string, $matches);
                        $supplier_initials = implode('', $matches[0]);
                        $temp_image['supplier_initials'] = strtoupper($supplier_initials);

                        if ($product_image->size != null) {
                            $temp_image['size'] = $product_image->size;
                        } else {
                            $temp_image['size'] = (string) $product_image->lmeasurement.', '.(string) $product_image->hmeasurement.', '.(string) $product_image->dmeasurement;
                        }
                    }

                    array_push($images_array, $temp_image);
                }

                $messageParams['images'] = $images_array;
            }

            $result[] = array_merge($params, $messageParams);
        }

        $result = array_values(collect($result)->sortBy('created_at')->reverse()->toArray());
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10000;

        if ($request->page) {
            $currentItems = array_slice($result, $perPage * ($currentPage - 1), $perPage);
        } else {
            $currentItems = array_reverse(array_slice($result, $perPage * ($currentPage - 1), $perPage));
            $result = array_reverse($result);
        }

        $result = new LengthAwarePaginator($currentItems, count($result), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return response()->json($result);
    }

    public function approveMessage($context, Request $request, $chat_id = 0): JsonResponse
    {
        $defCustomer = '971547763482';

        $message = ChatMessage::findOrFail($request->get('messageId'));
        $today_date = Carbon::now()->format('Y-m-d');
        $is_mail = 0;
        $model_id = '';
        $model_class = '';
        $toemail = '';
        $subject = $request->get('subject') ?? null;
        if ($chat_id > 0) {
            $m = ChatMessage::where('id', $chat_id)->first();

            if ($m) {
                $is_mail = $m->is_email;
            }
        }

        if ($context == 'customer') {
            // check the customer message
            $customer = Customer::find($message->customer_id);

            $model_id = $message->customer_id;
            $model_class = Customer::class;
            $toemail = $customer->email;

            if (Setting::get('whatsapp_number_change') == 1) {
                $customer = Customer::find($message->customer_id);
                $default_api = ApiKey::where('default', 1)->first();

                if (! $customer->whatsapp_number_change_notified() && $default_api->number != $customer->whatsapp_number) {
                    $params = [
                        'number' => null,
                        'user_id' => Auth::id(),
                        'approved' => 1,
                        'status' => 9,
                        'customer_id' => $message->customer_id,
                        'message' => 'Our whatsapp number has changed',
                    ];

                    $additional_message = ChatMessage::create($params);

                    CommunicationHistory::create([
                        'model_id' => $customer->id,
                        'model_type' => Customer::class,
                        'type' => 'number-change',
                        'method' => 'whatsapp',
                    ]);
                }
            }
            if (isset($customer)) {
                $phone = $customer->phone;
                $whatsapp_number = $customer->whatsapp_number;
            } else {
                $customer = Customer::find($message->customer_id);
                if ($customer) {
                    $phone = $customer->phone;
                    $whatsapp_number = $customer->whatsapp_number;
                }
            }
        } else {
            if ($context == 'supplier') {
                $supplier = Supplier::find($message->supplier_id);
                $phone = $supplier->default_phone;
                if (empty($supplier->whatsapp_number)) {
                    $whatsapp_number = '971502609192';
                } else {
                    $whatsapp_number = $supplier->whatsapp_number;
                }
                $toemail = $supplier->email;
                $model_id = $message->supplier_id;
                $model_class = Supplier::class;
            } else {
                if ($context == 'vendor') {
                    $vendor = Vendor::find($message->vendor_id);
                    $phone = $vendor->default_phone;
                    $whatsapp_number = $vendor->whatsapp_number;
                    $toemail = $vendor->email;
                    $model_id = $message->vendor_id;
                    $model_class = Vendor::class;
                } else {
                    if ($context == 'task') {
                        $sender = User::find($message->user_id);

                        $isUser = false;
                        if ($message->erp_user == '') {
                            $receiver = Contact::find($message->contact_id);
                        } else {
                            $isUser = true;
                            $receiver = User::find($message->erp_user);
                        }

                        $phone = @$receiver->phone;
                        $whatsapp_number = ($receiver && $isUser) ? $receiver->whatsapp_number : $sender->whatsapp_number;
                    } else {
                        if ($context == 'user') {
                            $sender = User::find($message->user_id);
                            $isUser = false;
                            if ($message->erp_user != '') {
                                $isUser = true;
                                $receiver = User::find($message->erp_user);
                            } else {
                                $receiver = Contact::find($message->contact_id);
                            }

                            $phone = $receiver->phone;
                            $whatsapp_number = ($receiver && $isUser) ? $receiver->whatsapp_number : $sender->whatsapp_number;
                        } else {
                            if ($context == 'dubbizle') {
                                $dubbizle = Dubbizle::find($message->dubbizle_id);
                                $phone = $dubbizle->phone_number;
                                $whatsapp_number = '971502609192';
                            } else {
                                if ($context == 'lawyer') {
                                    $lawyer = Lawyer::find($message->lawyer_id);
                                    $phone = $lawyer->default_phone;
                                    $whatsapp_number = $lawyer->whatsapp_number;
                                } else {
                                    if ($context == 'case') {
                                        $case = LegalCase::find($message->case_id);
                                        $lawyer = $case->lawyer;
                                        if ($lawyer) {
                                            $phone = $lawyer->default_phone;
                                        } else {
                                            $phone = '';
                                        }
                                        $whatsapp_number = $case->whatsapp_number;
                                    } else {
                                        if ($context == 'blogger') {
                                            $blogger = Blogger::find($message->blogger_id);
                                            $phone = $blogger->default_phone;
                                            $whatsapp_number = $blogger->whatsapp_number;
                                        } else {
                                            if ($context == 'old') {
                                                $old = Old::find($message->old_id);
                                                $phone = $old->phone;
                                                $whatsapp_number = '';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $data = '';

        if ($message->message != '') {
            if ($context == 'supplier' || $context == 'customer' || $context == 'vendor' || $context == 'task' || $context == 'charity' || $context == 'dubbizle' || $context == 'lawyer' || $context == 'case' || $context == 'blogger' || $context == 'old' || $context == 'hubstuff' || $context == 'user-feedback' || $context == 'user-feedback-hrTicket' || $context == 'SOP-Data' || $context == 'timedoctor' || $context == 'email') {
                if ($context == 'supplier') {
                    $supplierDetails = Supplier::find($message->supplier_id);
                    $language = $supplierDetails->language;
                    $model_id = $message->supplier_id;
                    $model_class = Supplier::class;
                    $toemail = $supplierDetails->email;
                    if ($language != null) {
                        try {
                            $result = TranslationHelper::translate('en', $language, $message->message);
                            $history = [
                                'msg_id' => $message->id,
                                'supplier_id' => $message->supplier_id,
                                'original_msg' => $message->message,
                                'translate_msg' => '('.$language.') '.$result,
                                'error_log' => 'N/A',
                            ];
                            SupplierTranslateHistory::insert($history);
                        } catch (\Throwable $e) {
                            $history = [
                                'msg_id' => $message->id,
                                'supplier_id' => $message->supplier_id,
                                'original_msg' => $message->message,
                                'translate_msg' => null,
                                'error_log' => $e->getMessage(),
                            ];
                            SupplierTranslateHistory::insert($history);
                            throw new Exception($e->getMessage(), 1);
                        }
                        $message->message = $result;
                    }
                }
                if ($context == 'customer') {
                    $supplierDetails = Customer::find($message->supplier_id);
                    $language = isset($supplierDetails) && $supplierDetails ? $supplierDetails->language : '';
                    if ($language != null) {
                        $result = TranslationHelper::translate('en', $language, $message->message);
                        $message->message = $result;
                    }
                }

                if ($context == 'user-feedback') {
                    $userDetails = User::find($message->user_id);
                    $model_id = $message->user_id;
                    $model_class = User::class;
                    $toemail = $userDetails->email;
                    $phone = $userDetails->phone;
                    $user = Auth::user();
                    $whatsapp_number = $user->whatsapp_number;
                    $language = $userDetails->language;
                    if ($language != null) {
                        $result = TranslationHelper::translate('en', $language, $message->message);
                        $message->message = $result;
                    }
                }
                if ($context == 'user-feedback-hrTicket') {
                    $userDetails = User::find($message->user_id);
                    $model_id = $message->user_id;
                    $model_class = User::class;
                    $toemail = $userDetails->email;
                    $phone = $userDetails->phone;
                    $user = Auth::user();
                    $whatsapp_number = $user->whatsapp_number;
                    $language = $userDetails->language;
                    if ($language != null) {
                        $result = TranslationHelper::translate('en', $language, $message->message);
                        $message->message = $result;
                    }
                }
                if ($context == 'charity') {
                    $msg = ChatMessage::where('id', $request->messageId)->first();
                    $charity = CustomerCharity::find($msg->charity_id);
                    $phone = $charity->phone;
                    $whatsapp_number = Auth::user()->whatsapp_number;
                    $model_id = $msg->charity_id;
                    $model_class = CustomerCharity::class;
                    $toemail = $charity->email;
                }
                if ($context == 'SOP-Data') {
                    $user = User::find($message->sop_user_id);

                    $phone = $user->phone;
                    $whatsapp_number = $user->whatsapp_number;
                    $model_id = $message->sop_user_id;
                    $toemail = $user->email;
                    $model_class = User::class;
                }
                if ($context == 'hubstuff') {
                    $user = User::find($message->hubstuff_activity_user_id);
                    $phone = $user->phone;
                    $toemail = $user->email;
                    $whatsapp_number = Auth::user()->whatsapp_number;
                    $model_id = $message->user_id;
                    $model_class = User::class;
                }

                if ($context == 'timedoctor') {
                    $user = User::find($message->time_doctor_activity_user_id);
                    $phone = $user->phone;
                    $toemail = $user->email;
                    $whatsapp_number = Auth::user()->whatsapp_number;
                    $model_id = $message->user_id;
                    $model_class = User::class;
                }

                if ($context == 'email') {
                    $emailObj = Email::find($message->email_id);
                    $toemail = $emailObj->to;
                    $model_id = $message->email_id;
                    $model_class = Email::class;
                }

                if ($is_mail == 1) {
                    $this->sendemail($message, $model_id, $model_class, $toemail, $chat_id, $subject);
                }
                if ($is_mail == 2) {
                    WebNotificationController::sendBulkNotification($message->user_id, $subject, $message->message);
                } else {
                    //
                }
            } else {
                if ($is_mail == 1) {
                    $this->sendemail($message, $model_id, $model_class, $toemail, $chat_id);
                }
                if ($is_mail == 2) {
                    WebNotificationController::sendBulkNotification($message->user_id, $subject, $message->message);
                } else {
                    //
                }
            }

            // Store send result
            if (isset($sendResult) && $sendResult) {
                $message->unique_id = $sendResult['id'] ?? '';
                $message->save();
            }
        }

        $sendMediaFile = true;
        if ($message->media_url != '') {
            // Store send result
            if (isset($sendResult) && $sendResult) {
                $message->unique_id = $sendResult['id'] ?? '';
                $message->save();
            }
            // check here that image media url is temp created if so we can delete that
            if (strpos($message->media_url, 'instant_message_') !== false) {
                $sendMediaFile = false;
                $path = parse_url($message->media_url, PHP_URL_PATH);
                if (file_exists(public_path($path)) && strpos($message->media_url, $path) !== false) {
                    @unlink(public_path($path));
                    $message->media_url = null;
                    $message->save();
                }
            }
        }

        $images = $message->getMedia(config('constants.media_tags'));
        if (! empty($images) && $sendMediaFile) {
            $count = 0;
            foreach ($images as $key => $image) {
                $send = str_replace(' ', '%20', getMediaUrl($image));

                if ($context == 'task' || $context == 'vendor' || $context == 'supplier') {
                    // Store send result
                    if (isset($sendResult) && $sendResult) {
                        $message->unique_id = $sendResult['id'] ?? '';
                        $message->save();
                    }
                } else {
                    if ($count < 5) {
                        $count++;
                    } else {
                        sleep(5);

                        $count = 0;
                    }

                    // Store send result
                    if (isset($sendResult) && $sendResult) {
                        $message->unique_id = $sendResult['id'] ?? '';
                        $message->save();
                    }
                }
            }
        }

        $message->update([
            'approved' => 1,
            'is_queue' => 0,
            'status' => 2,
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function sendToAll(SendToAllWhatsAppRequest $request, $validate = true): RedirectResponse
    {
        set_time_limit(0);

        $frequency = $request->frequency;

        if ($request->image_id != '') {
            $broadcast_image = BroadcastImage::find($request->image_id);
            if ($broadcast_image->hasMedia(config('constants.media_tags'))) {
                foreach ($broadcast_image->getMedia(config('constants.media_tags')) as $key2 => $brod_image) {
                    $content['image']['url'] = getMediaUrl($brod_image);
                    $content['image']['key'] = $brod_image->getKey();
                }
            }
        }
        //Broadcast For Whatsapp
        if (($request->to_all || $request->moduletype == 'customers') && $request->platform == 'whatsapp') {
            // Create empty array for checking numbers
            $arrCustomerNumbers = [];

            // Get all numbers from config
            $configs = WhatsappConfig::where('is_customer_support', 0)->get();

            //Loop over numbers
            foreach ($configs as $arrNumber) {
                if ($arrNumber['number']) {
                    $arrBroadcastNumbers[] = $arrNumber['number'];
                }
            }

            $minutes = round(60 / $frequency);

            $data = Customer::whereNotNull('phone')->where('do_not_disturb', 0);

            if ($request->rating != '') {
                $data = $data->where('rating', $request->rating);
            }

            if ($request->gender != '') {
                $data = $data->where('gender', $request->gender);
            }

            if ($request->shoe_size != '') {
                $data = $data->where('shoe_size', $request->shoe_size);
            }

            if ($request->clothing_size != '') {
                $data = $data->where('clothing_size', $request->clothing_size);
            }

            $data = $data->get()->groupBy('broadcast_number');

            foreach ($data as $broadcastNumber => $customers) {
                $now = $request->sending_time ? Carbon::parse($request->sending_time) : Carbon::now();
                $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $evening = Carbon::create($now->year, $now->month, $now->day, 19, 0, 0);

                if (! $now->between($morning, $evening, true)) {
                    if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                        // add day
                        $now->addDay();
                        $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                    } else {
                        // dont add day
                        $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                    }
                }

                if (in_array($broadcastNumber, $arrBroadcastNumbers)) {
                    foreach ($customers as $customer) {
                        //Changes put by satyam for connecting Old BroadCast with New BroadCast page
                        if (isset($customer->customerMarketingPlatformActive)) {
                            if ($customer->customerMarketingPlatformActive->active == 1) {
                                //Checking For DND
                                if ($customer->do_not_disturb == 1) {
                                    continue;
                                }

                                //Checking For Last Message Send 24 hours
                                if (isset($customer->lastImQueueSend) && $customer->lastImQueueSend->sent_at >= Carbon::now()->subDay()->toDateTimeString()) {
                                    continue;
                                }

                                //Check if customer has Phone
                                if ($customer->phone == '' || $customer->phone == null) {
                                    continue;
                                }

                                //Check if customer has broadcast
                                if ($customer->broadcast_number == '' || $customer->broadcast_number == null) {
                                    continue;
                                }

                                $params = [
                                    'number' => null,
                                    'user_id' => Auth::id(),
                                    'customer_id' => $customer->id,
                                    'approved' => 0,
                                    'status' => 8, // status for Broadcast messages
                                    'group_id' => $max_group_id,
                                ];

                                $priority = null; // Priority for broadcast messages, now the same as for normal messages
                                if ($request->image_id != null) {
                                    if ($content['image'] != null) {
                                        //Saving Message In Chat Message
                                        $chatMessage = ChatMessage::create($params);
                                        foreach ($content as $url) {
                                            //Attach image to chat message
                                            $chatMessage->attachMedia($url['key'], config('constants.media_tags'));
                                            $priority = 1;
                                            $send = InstantMessagingHelper::scheduleMessage($customer->phone, $customer->broadcast_number, $request->message, $url['url'], $priority, $now, $max_group_id);
                                            if ($send != false) {
                                                $now->addMinutes($minutes);
                                                $now = InstantMessagingHelper::broadcastSendingTimeCheck($now);
                                            }
                                        }
                                    }
                                } elseif ($request->linked_images == null) {

                                    $send = InstantMessagingHelper::scheduleMessage($customer->phone, $customer->broadcast_number, $request->message, '', $priority, $now, $max_group_id);
                                    if ($send != false) {
                                        $now->addMinutes($minutes);
                                        $now = InstantMessagingHelper::broadcastSendingTimeCheck($now);
                                    }
                                }

                                //DO NOT REMOVE THIS CODE
                                // MessageQueue::create([
                                //     'user_id' => Auth::id(),
                                //     'customer_id' => $customer->id,
                                //     'phone' => null,
                                //     'type' => 'message_all',
                                //     'data' => json_encode($content),
                                //     'sending_time' => $now,
                                //     'group_id' => $max_group_id
                                // ]);
                            }
                        }
                    }
                }
            }
            //Broadcast for Facebook
        } elseif (strtolower($request->platform) == 'facebook') {
            //Getting Max Id
            $max_group_id = ChatMessage::where('status', 8)->max('group_id') + 1;

            //Getting All Brand Fans
            $brands = BrandFans::all();

            $count = 0;

            //Scheduling Time based on frequency
            $now = $request->sending_time ? Carbon::parse($request->sending_time) : Carbon::now();
            $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
            $evening = Carbon::create($now->year, $now->month, $now->day, 19, 0, 0);

            if (! $now->between($morning, $evening, true)) {
                if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                    // add day
                    $now->addDay();
                    $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                } else {
                    // dont add day
                    $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                }
            }
            $sendingTime = '';

            //Getting Last Broadcast Id
            $broadcastId = ImQueue::groupBy('broadcast_id')->orderByDesc('broadcast_id')->first();

            foreach ($brands as $brand) {
                $count++;

                // Convert maxTime to unixtime
                if (empty($sendingTime)) {
                    $maxTime = strtotime($now);
                } else {
                    $now = $sendingTime ? Carbon::parse($sendingTime) : Carbon::now();
                    $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                    $evening = Carbon::create($now->year, $now->month, $now->day, 19, 0, 0);

                    if (! $now->between($morning, $evening, true)) {
                        if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                            // add day
                            $now->addDay();
                            $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                        } else {
                            // dont add day
                            $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                        }
                    }
                    $sendingTime = $now;
                    $maxTime = strtotime($sendingTime);
                }

                // Add interval
                $maxTime = $maxTime + (3600 / $request->frequency);

                // Check if it's in the future
                if ($maxTime < time()) {
                    $maxTime = time();
                }

                $sendAfter = date('Y-m-d H:i:s', $maxTime);
                $sendingTime = $sendAfter;
                //Getting Least Number of Messages Send Per Account
                $accounts = Account::where('platform', 'facebook')->where('status', 1)->get();
                $count = [];
                foreach ($accounts as $account) {
                    $count[] = [$account->imQueueBroadcast->count() => $account->last_name];
                }
                //Arranging In Ascending Order
                ksort($count);
                if (! isset($broadcastId->broadcast_id)) {
                    $broadcastIdLast = 0;
                } else {
                    $broadcastIdLast = $broadcastId->broadcast_id;
                }
                //Just Sending Text To Facebook
                if (isset($content)) {
                    foreach ($content as $url) {
                        if (isset($count[0][key($count[0])])) {
                            $username = $count[0][key($count[0])];
                            $queue = new ImQueue;
                            $queue->im_client = 'facebook';
                            $queue->number_to = str_replace('https://www.facebook.com/', '', $brand->profile_url);
                            $queue->number_from = $username;
                            $queue->text = $request->message;
                            $queue->priority = null;
                            $queue->image = $url['url'];
                            $queue->marketing_message_type_id = 1;
                            $queue->priority = 1;
                            $queue->broadcast_id = ($broadcastIdLast + 1);
                            $queue->send_after = $sendAfter;
                            $queue->save();
                        }
                    }
                } else {
                    //Sending Text with Image
                    if (isset($count[0][key($count[0])])) {
                        $username = $count[0][key($count[0])];
                        $queue = new ImQueue;
                        $queue->im_client = 'facebook';
                        $queue->number_to = str_replace('https://www.facebook.com/', '', $brand->profile_url);
                        $queue->number_from = $username;
                        $queue->text = $request->message;
                        $queue->priority = null;
                        $queue->priority = 1;
                        $queue->marketing_message_type_id = 1;
                        $queue->broadcast_id = ($broadcastId->broadcast_id + 1);
                        $queue->send_after = $sendAfter;
                        $queue->save();
                    }
                }
            }
        } elseif (strtolower($request->platform) == 'instagram') {
            //Getting Cold Leads to Send Message
            $query = ColdLeads::query();
            $competitor = $request->competitor;
            $limit = 100;
            //Check if competitor is selected
            if (! empty($competitor)) {
                $comp = CompetitorPage::find($competitor);
                $query = $query->where('because_of', 'LIKE', '%via '.$comp->name.'%');
            }
            //check for gender
            if (! empty($request->gender)) {
                $query = $query->where('gender', $request->gender);
            }
            //Get Cold Leads to be send
            $coldleads = $query->where('status', 1)->where('messages_sent', '<', 5)->take($limit)->orderBy('messages_sent')->orderBy('id')->get();

            $count = 0;

            //Scheduling Time based on frequency
            $now = $request->sending_time ? Carbon::parse($request->sending_time) : Carbon::now();
            $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
            $evening = Carbon::create($now->year, $now->month, $now->day, 19, 0, 0);

            if (! $now->between($morning, $evening, true)) {
                if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                    // add day
                    $now->addDay();
                    $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                    $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                    $evening = Carbon::create($now->year, $now->month, $now->day, 19, 0, 0);
                } else {
                    // dont add day
                    $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                    $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                    $evening = Carbon::create($now->year, $now->month, $now->day, 19, 0, 0);
                }
            }
            $sendingTime = '';
            //Getting Last Broadcast Id
            $broadcastId = ImQueue::groupBy('broadcast_id')->orderByDesc('broadcast_id')->first();

            foreach ($coldleads as $coldlead) {
                $count++;

                // Convert maxTime to unixtime
                if (empty($sendingTime)) {
                    $maxTime = strtotime($now);
                } else {
                    $now = $sendingTime ? Carbon::parse($sendingTime) : Carbon::now();
                    $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                    $evening = Carbon::create($now->year, $now->month, $now->day, 19, 0, 0);

                    if (! $now->between($morning, $evening, true)) {
                        if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                            // add day
                            $now->addDay();
                            $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                        } else {
                            // dont add day
                            $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                        }
                    }
                    $sendingTime = $now;
                    $maxTime = strtotime($sendingTime);
                }

                // Add interval
                $maxTime = $maxTime + (3600 / $request->frequency);

                // Check if it's in the future
                if ($maxTime < time()) {
                    $maxTime = time();
                }

                $sendAfter = date('Y-m-d H:i:s', $maxTime);
                $sendingTime = $sendAfter;

                //Getting Least Number of Messages Send Per Account
                $accounts = Account::where('platform', 'instagram')->where('status', 1)->get();
                $count = [];
                foreach ($accounts as $account) {
                    $count[] = [$account->imQueueBroadcast->count() => $account->last_name];
                }
                //Arranging In Ascending Order
                ksort($count);

                if (! isset($broadcastId->broadcast_id)) {
                    $broadcastIdLast = 0;
                } else {
                    $broadcastIdLast = $broadcastId->broadcast_id;
                }
                //Sending Text with Image
                if (isset($count[0][key($count[0])])) {
                    $username = $count[0][key($count[0])];
                    $queue = new ImQueue;
                    $queue->im_client = 'instagram';
                    $queue->number_to = $coldlead->platform_id;
                    $queue->number_from = $username;
                    $queue->text = $request->message;
                    $queue->priority = null;
                    $queue->priority = 1;
                    $queue->marketing_message_type_id = 1;
                    $queue->broadcast_id = ($broadcastIdLast + 1);
                    $queue->send_after = $sendAfter;
                    $queue->save();
                }
            }
        } else {
            $minutes = round(60 / $frequency);
            $now = $request->sending_time ? Carbon::parse($request->sending_time) : Carbon::now();
            $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
            $evening = Carbon::create($now->year, $now->month, $now->day, 18, 0, 0);
            $max_group_id = MessageQueue::max('group_id') + 1;
            $array = Excel::toArray(new CustomerNumberImport, $request->file('file'));

            foreach ($array as $item) {
                foreach ($item as $it) {
                    $number = (int) $it[0];

                    if (! $now->between($morning, $evening, true)) {
                        if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                            // add day
                            $now->addDay();
                            $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                        } else {
                            // dont add day
                            $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                        }
                    }

                    MessageQueue::create([
                        'user_id' => Auth::id(),
                        'customer_id' => null,
                        'phone' => $number,
                        'whatsapp_number' => $request->whatsapp_number,
                        'type' => 'message_selected',
                        'data' => json_encode($content),
                        'sending_time' => $now,
                        'group_id' => $max_group_id,
                    ]);

                    $now->addMinutes($minutes);
                }
            }
        }

        return redirect()->route('broadcast.images')->with('success', 'Messages are being sent in the background!');
    }

    public function resendMessage2(Request $request)
    {
        $messageId = $request->get('message_id');
        $message = ChatMessage::find($messageId);

        $requestData = new Request;
        $requestData->setMethod('POST');
        $requestData->request->add(['customer_id' => $message->customer_id, 'message' => $message->message, 'status' => 1]);

        return $this->sendMessage($requestData, 'customer', true);
    }

    public function stopAll(): RedirectResponse
    {
        $message_queues = ImQueue::whereNull('sent_at')->get();

        foreach ($message_queues as $message_queue) {
            $message_queue->send_after = null;
            $message_queue->save();
        }

        return redirect()->back()->with('success', 'Messages stopped processing!');
    }

    public function sendWithWhatsApp($number, $sendNumber, $text, $validation = true, $chat_message_id = null)
    {
        $logDetail = [
            'number' => $number,
            'whatsapp_number' => $sendNumber,
            'message' => $text,
            'validation' => $validation,
            'chat_message_id' => $chat_message_id,
        ];
        if ($validation == true) {
            if (Auth::id() != 3) {
                if (strlen($number) != 12 || ! preg_match('/^[91]{2}/', $number)) {
                    // DON'T THROW EXCEPTION
                    // throw new \Exception("Invalid number format. Must be 12 digits and start with 91");
                    Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Invalid number format. Must be 12 digits and start with 91: '.$number.' ['.json_encode($logDetail).'] ');

                    return false;
                }
            }
        }

        $api_keys = ApiKey::all();

        if ($api_keys->contains('number', $number)) {
            return true; // Customer number already exists, returning true
        }

        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();

        if (Setting::get('whatsapp_number_change') == 1) {
            $keys = config('apiwha.api_keys');
            $key = $keys[0]['key'];

            foreach ($api_keys as $api_key) {
                if ($api_key->default == 1) {
                    $key = $api_key->key;
                }
            }
        } else {
            if (is_null($sendNumber)) {
                $keys = config('apiwha.api_keys');
                $key = $keys[0]['key'];

                foreach ($api_keys as $api_key) {
                    if ($api_key->default == 1) {
                        $key = $api_key->key;
                    }
                }
            } else {
                $keys = config('apiwha.api_keys');
                $key = $keys[0]['key'];

                foreach ($api_keys as $api_key) {
                    if ($api_key->default == 1) {
                        $key = $api_key->key;
                    }
                }

                foreach ($api_keys as $api_key) {
                    if ($api_key->number == $sendNumber) {
                        $key = $api_key->key;
                    }
                }
            }
        }

        $encodedNumber = urlencode($number);
        $encodedText = urlencode($text);

        if ($chat_message_id) {
            $custom_data = [
                'chat_message_id' => $chat_message_id,
            ];

            $encodedCustomData = urlencode(json_encode($custom_data));
        } else {
            $encodedCustomData = '';
        }
        $url = 'https://panel.apiwha.com/send_message.php?apikey='.$key.'&number='.$encodedNumber.'&text='.$encodedText.'&custom_data='.$encodedCustomData;
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        LogRequest::log($startTime, $url, 'GET', json_encode($logDetail), $response, $httpcode, WhatsAppController::class, 'sendWithWhatsApp');

        if ($err) {
            // DON'T THROW EXCEPTION
            // throw new \Exception("cURL Error #:" . $err);
            Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') cURL Error for number '.$number.':'.$err.' ['.json_encode($logDetail).'] ');

            return false;
        } else {
            $result = json_decode($response);
            if (! $result->success) {
                // DON'T THROW EXCEPTION
                //throw new \Exception("whatsapp request error: " . $result->description);
                Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') WhatsApp request error for number '.$number.': '.$result->description.' ['.json_encode($logDetail).'] ');

                return false;
            } else {
                // Log successful send
                Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Message was sent to number '.$number.':'.$response.' ['.json_encode($logDetail).'] ');
            }
        }
    }

    public function pullApiwha()
    {
        $curl = curl_init();

        $key = 'Z802FWHI8E2OP0X120QR';

        $encodedNumber = urlencode('917534013101');
        $number = '917534013101';
        $encodedType = urlencode('IN');

        $url = 'https://panel.apiwha.com/get_messages.php?apikey='.$key.'&type='.$encodedType.'&number='.$encodedNumber;
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $parameters = [];
        curl_close($curl);

        LogRequest::log($startTime, $url, 'GET', json_encode($parameters), json_decode($response), $httpcode, WhatsAppController::class, 'pullApiwha');

        if ($err) {
            // DON'T THROW EXCEPTION
            // throw new \Exception( "cURL Error #:" . $err );
            Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') cURL Error for number '.$number.':'.$err);

            return false;
        } else {
            $result = json_decode($response, true);
        }

        $filtered_data = [];

        foreach ($result as $item) {
            if (Carbon::parse($item['creation_date'])->gt(Carbon::parse('2019-06-17 00:00:00'))) {
                $filtered_data[] = $item;
                $customer = $this->findCustomerByNumber($item['from']);

                if ($customer) {
                    $params = [
                        'number' => $item['from'],
                        'customer_id' => $customer->id,
                        'message' => $item['text'],
                        'created_at' => $item['creation_date'],
                    ];
                }
            }
        }

        return $result;
    }

    public function sendWithNewApi($number, $whatsapp_number = null, $message = null, $file = null, $chat_message_id = null, $enqueue = 'opportunistic')
    {
        $logDetail = [
            'number' => $number,
            'whatsapp_number' => $whatsapp_number,
            'message' => $message,
            'file' => $file,
            'chat_message_id' => $chat_message_id,
            'enqueue' => $enqueue,
        ];

        $configs = config('wassenger.api_keys');
        $encodedNumber = '+'.$number;
        $encodedText = $message;
        $wa_token = $configs[0]['key'];

        if ($whatsapp_number != null) {
            foreach ($configs as $key => $config) {
                if ($config['number'] == $whatsapp_number) {
                    $wa_device = $config['device'];

                    break;
                }

                $wa_device = $configs[0]['device'];
            }
        } else {
            $wa_device = $configs[0]['device'];
        }

        if ($file != null) {
            $file_exploded = explode('/', $file);
            $encoded_part = str_replace('%25', '%', urlencode(str_replace(' ', '%20', $file_exploded[count($file_exploded) - 1])));
            array_pop($file_exploded);
            array_push($file_exploded, $encoded_part);

            $file_encoded = implode('/', $file_exploded);

            $array = [
                'url' => "$file_encoded",
            ];

            $curl = curl_init();
            $url = "https://api.wassenger.com/v1/files?reference=$chat_message_id";

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 180,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($array),
                CURLOPT_HTTPHEADER => [
                    'content-type: application/json',
                    "token: $wa_token",
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);

            curl_close($curl);

            LogRequest::log($startTime, $url, 'POST', json_encode($array), json_decode($response), $httpcode, WhatsAppController::class, 'sendWithNewApi');
            // throw new \Exception("cURL Error #: whatttt");
            if ($err) {
                // DON'T THROW EXCEPTION
                //throw new \Exception( "cURL Error #:" . $err );
                Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') cURL Error for number '.$number.':'.$err.' ['.json_encode($logDetail).'] ');

                return false;
            } else {
                $result = json_decode($response, true);

                if (is_array($result)) {
                    if (array_key_exists('status', $result)) {
                        if ($result['status'] == 409) {
                            $image_id = $result['meta']['file'];
                        } else {
                            // DON'T THROW EXCEPTION
                            // throw new \Exception( "Something was wrong with image: " . $result[ 'message' ] );
                            Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Something was wrong with the image for number '.$number.':'.$result['message'].' ['.json_encode($logDetail).'] ');

                            return false;
                        }
                    } else {
                        $image_id = $result[0]['id'];
                    }
                }
            }
        }
        $array = [
            'phone' => $encodedNumber,
            'message' => (string) $encodedText,
            'reference' => (string) $chat_message_id,
            'device' => "$wa_device",
            'enqueue' => "$enqueue",
        ];

        if (isset($image_id)) {
            $array['media'] = [
                'file' => "$image_id",
            ];
        }

        $curl = curl_init();
        $url = 'https://api.wassenger.com/v1/messages';

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 180,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($array),
            CURLOPT_HTTPHEADER => [
                'content-type: application/json',
                "token: $wa_token",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        LogRequest::log($startTime, $url, 'POST', json_encode($array), json_decode($response), $httpcode, WhatsAppController::class, 'sendWithNewApi');

        if ($err) {
            // DON'T THROW EXCEPTION
            // throw new \Exception( "cURL Error #:" . $err );
            Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') cURL Error for number '.$number.':'.$err.' ['.json_encode($logDetail).'] ');

            return false;
        } else {
            $result = json_decode($response, true);

            if ($http_code != 201) {
                // DON'T THROW EXCEPTION
                // throw new \Exception( "Something was wrong with message: " . $response );
                Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Something was wrong with the message for number '.$number.':'.$response.' ['.json_encode($logDetail).'] ');

                return false;
            } else {
                // Log successful send
                Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Message was sent to number '.$number.':'.$response.' ['.json_encode($logDetail).'] ');
            }
        }

        return $result;
    }

    public function sendWithThirdApi($number, $whatsapp_number = null, $message = null, $file = null, $chat_message_id = null, $enqueue = 'opportunistic', $customer_id = null)
    {
        $logDetail = [
            'number' => $number,
            'whatsapp_number' => $whatsapp_number,
            'message' => $message,
            'file' => $file,
            'chat_message_id' => $chat_message_id,
            'enqueue' => $enqueue,
            'customer_id' => $customer_id,
        ];

        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        // Get configs
        $config = WhatsappConfig::getWhatsappConfigs();
        $whatAppConfig = WhatsappConfig::where('number', $whatsapp_number)->where('status', 1)->first();
        if (! $whatAppConfig) {
            // check if number is set or not then call from the table
            if (! isset($config[$whatsapp_number])) {
                $whatsappRecord = WhatsappConfig::where('provider', 'wassenger')
                    ->where('instance_id', '!=', '')
                    ->where('token', '!=', '')
                    ->where('status', 1)
                    ->where('number', $whatsapp_number)
                    ->first();

                if ($whatsappRecord) {
                    $config[$whatsapp_number] = [
                        'instance_id' => $whatsappRecord->instance_id,
                        'token' => $whatsappRecord->token,
                        'is_use_own' => $whatsappRecord->is_use_own,
                    ];
                }
            }
        } else {
            if ($whatAppConfig->provicer == 'official-whatsapp') {
                $whatsappAccount = WhatsappBusinessAccounts::where('id', $whatAppConfig->instance_id)->first();
                $config[$whatsapp_number] = [
                    'provider' => 'official-whatsapp',
                    'instance_id' => $whatsappAccount->id,
                    'token' => $whatsappAccount->business_access_token,
                    'is_use_own' => $whatAppConfig->is_use_own,
                ];
            } else {
                $config[$whatsapp_number] = [
                    'instance_id' => $whatAppConfig->instance_id,
                    'token' => $whatAppConfig->token,
                    'is_use_own' => $whatAppConfig->is_use_own,
                ];
            }
        }

        $chatMessage = null;
        if ($chat_message_id > 0) {
            $chatMessage = ChatMessage::find($chat_message_id);
        }
        // Set instanceId and token
        $isUseOwn = false;
        if (isset($config[$whatsapp_number])) {
            $instanceId = $config[$whatsapp_number]['instance_id'];
            $token = $config[$whatsapp_number]['token'];
            $isUseOwn = isset($config[$whatsapp_number]['is_use_own']) ? $config[$whatsapp_number]['is_use_own'] : 0;
        } else {
            Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Whatsapp config not found for number '.$whatsapp_number.' while sending to number '.$number.' ['.json_encode($logDetail).'] ');
            $instanceId = $config[0]['instance_id'];
            $token = $config[0]['token'];
            $isUseOwn = isset($config[0]['is_use_own']) ? $config[0]['is_use_own'] : 0;
        }
        if (isset($customer_id) && $message != null && $message != '') {
            $customer = Customer::findOrFail($customer_id);

            $fields = ['[[NAME]]' => $customer->name, '[[CITY]]' => $customer->city, '[[EMAIL]]' => $customer->email, '[[PHONE]]' => $customer->phone, '[[PINCODE]]' => $customer->pincode, '[[WHATSAPP_NUMBER]]' => $customer->whatsapp_number, '[[SHOESIZE]]' => $customer->shoe_size, '[[CLOTHINGSIZE]]' => $customer->clothing_size];

            preg_match_all("/\[[^\]]*\]]/", $message, $matches);
            $values = $matches[0];

            foreach ($values as $value) {
                if (isset($fields[$value])) {
                    $message = str_replace($value, $fields[$value], $message);
                }
            }
        }

        $encodedNumber = '+'.$number;
        if ($isUseOwn == 1) {
            $encodedNumber = $number;
        }

        $encodedText = $message;

        $array = [
            'phone' => $encodedNumber,
        ];

        if ($encodedText != null && $file == null) {
            $array['body'] = $encodedText;
            $link = 'sendMessage';
        } else {
            $exploded = explode('/', $file);
            $filename = end($exploded);
            $array['body'] = $file;
            $array['filename'] = $filename;
            $link = 'sendFile';
            $array['caption'] = $encodedText;
        }

        $array['instanceId'] = $instanceId;
        // here is we call python
        if ($isUseOwn == 1) {
            $domain = 'http://167.86.89.241:82/'.$link;
        } else {
            if (isset($config[$whatsapp_number]['provider']) && $config[$whatsapp_number]['provider'] == 'wassenger') {
                $domain = 'https://api.wassenger.com/v1/messages?token='.$token;
                $array['message'] = $array['body'];
                $array['device'] = $array['instanceId'];
                unset($array['body']);
                unset($array['instanceId']);
            } elseif (isset($config[$whatsapp_number]['provider']) && $config[$whatsapp_number]['provider'] == 'official-whatsapp') {
                $apiCalled = true;
                $whatsappApiController = new WhatsAppOfficialController($config[$whatsapp_number]['instance_id']);
                $response = $whatsappApiController->sendMessage([
                    'type' => 'text',
                    'body' => $message,
                    'preview_url' => true,
                    'number' => $whatsapp_number,
                ]);
                if ($response['status']) {
                    if ($chatMessage) {
                        $chatMessage->unique_id = $response['data']['messages'][0]['id'];
                        $chatMessage->error_status = ChatMessage::ERROR_STATUS_SUCCESS;
                        $chatMessage->save();
                    }
                    Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Message was sent to number '.$number.':'.$response['data']['messages'][0]['id'].' ['.json_encode($logDetail).'] ');

                    return $response['data']['messages'][0];
                } else {
                    Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') cURL Error for number '.$number.': ['.json_encode($logDetail).'] ');
                    if ($chatMessage) {
                        $chatMessage->error_status = ChatMessage::ERROR_STATUS_ERROR;
                        $chatMessage->error_info = json_encode($response);
                        $chatMessage->save();
                    }

                    return $response;
                }
            } else {
                $domain = "https://api.chat-api.com/instance$instanceId/$link?token=$token";
            }
        }

        $customerrequest_arr['CUSTOMREQUEST'] = 'POST';
        $message_arr['message'] = $message;
        $file_arr['file'] = $file;

        $log_data = [
            'Message_Data' => $message_arr,
            'Customer_request_data' => $customerrequest_arr,
            'PostFields' => $array,
            'file_data' => $file_arr,
            'logDetail_data' => $logDetail,
        ];

        $str_log = 'Message :: '.json_encode($message).' || Customer Request :: POST || Post Fields :: '.json_encode($array).' || File :: '.$file.' || Log Details :: '.json_encode($logDetail);

        Log::channel('chatapi')->debug('cUrl_url:{"'.$domain." } \nMessage: ".$str_log);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $domain,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($array),
            CURLOPT_HTTPHEADER => [
                'content-type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        LogRequest::log($startTime, $domain, 'POST', json_encode($array), json_decode($response), $httpcode, WhatsAppController::class, 'sendWithThirdApi');

        if ($err) {
            // DON'T THROW EXCEPTION
            //throw new \Exception("cURL Error #:" . $err);
            Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') cURL Error for number '.$number.':'.$err.' ['.json_encode($logDetail).'] ');
            if ($chatMessage) {
                $chatMessage->error_status = ChatMessage::ERROR_STATUS_ERROR;
                $chatMessage->error_info = json_encode(['number' => $number, 'error' => $err]);
                $chatMessage->save();
            }

            return false;
        } else {
            // Log curl response

            $customerrequest_arr['CUSTOMREQUEST'] = 'POST';
            $message_arr1['message'] = $message;
            $file_arr1['file'] = $file;

            $log_data_send = [
                'Message_Data' => $message_arr1,
                'file_data' => $file_arr1,
                'logDetail_data' => $logDetail,
            ];

            $str_log = 'Message :: '.json_encode($message).' || File :: '.$file.' || Log Details :: '.json_encode($logDetail);

            Log::channel('chatapi')->debug('cUrl:'.$response."\nMessage: ".$str_log);

            // Json decode response into result
            $result = json_decode($response, true);

            // throw new \Exception("Something was wrong with message: " . $response);
            if (! is_array($result) || array_key_exists('sent', $result) && ! $result['sent']) {
                // DON'T THROW EXCEPTION
                //throw new \Exception("Something was wrong with message: " . $response);
                Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Something was wrong with the message for number '.$number.': '.$response.' ['.json_encode($logDetail).'] ');
                if ($chatMessage) {
                    $chatMessage->error_status = ChatMessage::ERROR_STATUS_ERROR;
                    $chatMessage->error_info = json_encode(['number' => $number, 'error' => $response]);
                    $chatMessage->save();
                }

                return false;
            } else {
                // Log successful send
                if ($chatMessage) {
                    $chatMessage->error_status = ChatMessage::ERROR_STATUS_SUCCESS;
                    $chatMessage->save();
                }
                Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Message was sent to number '.$number.':'.$response.' ['.json_encode($logDetail).'] ');
            }
        }

        return $result;
    }

    private function modifyParamsWithMessage($params, $data)
    {
        if (filter_var($data['text'], FILTER_VALIDATE_URL)) {
            // you're good
            $path = $data['text'];
            $paths = explode('/', $path);
            $file = $paths[count($paths) - 1];
            $extension = (isset($data['extension']) ? $data['extension'] : explode('.', $file)[1]);
            $fileName = uniqid(true).'.'.$extension;
            $contents = file_get_contents($path);
            if (file_put_contents(implode(DIRECTORY_SEPARATOR, [config('apiwha.media_path'), $fileName]), $contents) == false) {
                return false;
            }
            $url = implode('/', [config('app.url'), 'apiwha', 'media', $fileName]);
            $params['media_url'] = $url;
            $params['message'] = '';

            return $params;
        }
        $params['message'] = $data['text'];

        return $params;
    }

    public function updatestatus(Request $request): \Illuminate\Http\Response
    {
        $message = ChatMessage::find($request->get('id'));
        $message->status = $request->get('status');
        $message->save();

        if ($request->id && $request->status == 5) {
            ChatMessagesQuickData::updateOrCreate([
                'model' => Customer::class,
                'model_id' => $request->id,
            ], [
                'last_unread_message' => '',
                'last_unread_message_at' => null,
                'last_unread_message_id' => null,
            ]);
        }

        return response('success');
    }

    public function fixMessageError(Request $request, $id): \Illuminate\Http\Response
    {
        $chat_message = ChatMessage::find($id);

        if ($customer = Customer::find($chat_message->customer_id)) {
            $customer->is_error_flagged = 0;
            $customer->save();

            $messages = ChatMessage::where('customer_id', $customer->id)->where('error_status', '!=', 0)->get();

            foreach ($messages as $message) {
                $message->error_status = 0;
                $message->save();
            }
        }

        return response('success');
    }

    public function resendMessage(Request $request, $id): JsonResponse
    {
        $chat_message = ChatMessage::find($id);
        if ($customer = Customer::find($chat_message->customer_id)) {
            $chat_message->update([
                'resent' => $chat_message->resent + 1,
            ]);

            return response()->json([
                'resent' => $chat_message->resent,
            ]);
        }

        if ($chat_message->erp_user != '' || $chat_message->contact_id != '') {
            $sender = User::find($chat_message->user_id);
            if ($chat_message->erp_user != '') {
                $receiver = User::find($chat_message->erp_user);
            } else {
                $receiver = Contact::find($chat_message->contact_id);
            }

            $phone = $receiver->phone;
            $whatsapp_number = ($sender) ? $sender->whatsapp_number : null;
            $sending_message = $chat_message->message;

            if (preg_match_all("/Resent ([\d]+) times/i", $sending_message, $match)) {
                $sending_message = str_replace("/Resent ([\d]+) times/i", 'Resent '.($chat_message->resent + 1).' times', $sending_message);
            } else {
                $sending_message = 'Resent '.($chat_message->resent + 1).' times. '.$sending_message;
            }

            $params = [
                'user_id' => $chat_message->user_id,
                'number' => null,
                'task_id' => $chat_message->task_id,
                'developer_task_id' => $chat_message->developer_task_id,
                'erp_user' => $chat_message->erp_user,
                'contact_id' => $chat_message->contact_id,
                'message' => $sending_message,
                'resent' => $chat_message->resent + 1,
                'approved' => 1,
                'status' => 2,
            ];

            $new_message = ChatMessage::create($params);

            if ($chat_message->hasMedia(config('constants.attach_image_tag'))) {
                foreach ($chat_message->getMedia(config('constants.attach_image_tag')) as $image) {
                    $new_message->attachMedia($image, config('constants.media_tags'));
                }
            }

            if ($task = Task::find($chat_message->task_id)) {
                if (count($task->users) > 0) {
                    if ($task->assign_from == Auth::id()) {
                        foreach ($task->users as $key => $user) {
                            if ($key != 0) {
                                //
                            }
                        }
                    } else {
                        foreach ($task->users as $key => $user) {
                            if ($key != 0) {
                                if ($user->id != Auth::id()) {
                                    //
                                }
                            }
                        }
                    }
                }
            }

            $chat_message->update([
                'resent' => $chat_message->resent + 1,
            ]);
        }

        if ($chat_message->vendor_id != '') {
            $vendor = Vendor::find($chat_message->vendor_id);

            if ($vendor) {
                if ($chat_message->message != '') {
                    //
                }

                $chat_message->update([
                    'resent' => $chat_message->resent + 1,
                ]);
            }
        }

        if ($chat_message->supplier_id != '') {
            $supplier = Supplier::find($chat_message->supplier_id);

            if ($supplier) {
                if ($chat_message->additional_data != '') {
                    $additional_data_arr = json_decode($chat_message->additional_data);
                    $path = $additional_data_arr->attachment[0];
                    $subject = 'Product order';
                    $message = 'Please check below product order request';
                    if ($path != '') {
                        $emailClass = (new PurchaseExport($path, $subject, $message))->build();

                        $email = Email::create([
                            'model_id' => $supplier->id,
                            'model_type' => Supplier::class,
                            'from' => 'buying@amourint.com',
                            'to' => $supplier->email,
                            'subject' => $subject,
                            'message' => $message,
                            'template' => 'purchase-simple',
                            'additional_data' => json_encode(['attachment' => [$path]]),
                            'status' => 're-send',
                            'is_draft' => 0,
                        ]);

                        SendEmail::dispatch($email)->onQueue('send_email');
                    }
                }

                $chat_message->update([
                    'resent' => $chat_message->resent + 1,
                ]);
            }
        }

        return response()->json([
            'resent' => $chat_message->resent,
        ]);
    }

    public function createGroup($task_id, $group_id, $number, $message, $whatsapp_number)
    {
        $encodedText = $message;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        if ($whatsapp_number == '919004780634') { // Indian
            $instanceId = '43281';
            $token = 'yi841xjhrwyrwrc7';
        } elseif ($whatsapp_number == '971502609192') { // YM Dubai
            $instanceId = '62439';
            $token = 'jdcqh3ladeuvwzp4';
        } else {
            if ($whatsapp_number == '971562744570') { // Solo 06
                $instanceId = '55202';
                $token = '42ndn0qg5om26vzf';
            } else {
                if ($whatsapp_number == '971547763482') { // 04
                    $instanceId = '55211';
                    $token = '3b92u5cbg215c718';
                } else {
                    $instanceId = '62439';
                    $token = 'jdcqh3ladeuvwzp4';
                }
            }
        }

        if ($task_id != null) {
            $id = (string) $task_id;

            $array = [
                'groupName' => $id,
                'phones' => $number,

            ];
            $link = 'group';
        } else {
            $id = (string) $group_id;

            $array = [
                'groupId' => $id,
                'participantPhone' => $number,
            ];
            $link = 'addGroupParticipant';
        }

        $curl = curl_init();
        $url = "https://api.chat-api.com/instance$instanceId/$link?token=$token";

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($array),
            CURLOPT_HTTPHEADER => [
                'content-type: application/json',
                // "token: $wa_token"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        LogRequest::log($startTime, $url, 'POST', json_encode($array), json_decode($response), $httpcode, WhatsAppController::class, 'sendBulkNotification');
        if ($err) {
            // DON'T THROW EXCEPTION
            //throw new \Exception("cURL Error #:" . $err);
            Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') cURL Error for number '.$number.':'.$err);

            return false;
        } else {
            $result = json_decode($response, true);
            // throw new \Exception("Something was wrong with message: " . $response);
            if (! is_array($result) || array_key_exists('sent', $result) && ! $result['sent']) {
                // DON'T THROW EXCEPTION
                //throw new \Exception("Something was wrong with message: " . $response);
                Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Something was wrong with the message for number '.$response);

                return false;
            } else {
                // Log successful send
                Log::channel('whatsapp')->debug('(file '.__FILE__.' line '.__LINE__.') Message was sent to number '.$response);
            }
        }

        return $result;
    }

    public function saveProductFromSupplierIncomingImages($id, $imageUrl)
    {
        //FInd Supplier
        $supplier = Supplier::find($id);

        //get sku
        $lastQuickSellProduct = Product::select('sku')->where('sku', 'LIKE', '%QUICKSELL'.date('yz').'%')->orderByDesc('id')->first();

        try {
            if ($lastQuickSellProduct) {
                $number = str_ireplace('QUICKSELL', '', $lastQuickSellProduct->sku) + 1;
            } else {
                $number = date('yz').sprintf('%02d', 1);
            }
        } catch (Exception $e) {
            $number = 0;
        }

        $product = new Product;
        $product->name = 'QUICKSELL';
        $product->sku = 'QuickSell'.$number;
        $product->size = '';
        $product->brand = null;
        $product->color = '';
        $product->location = '';
        $product->category = '';
        $product->supplier = $supplier->supplier;
        $product->price = 0;
        $product->price_inr_special = 0;
        $product->stock = 1;
        $product->quick_product = 1;
        $product->is_pending = 0;
        $product->save();
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $imageUrl, $match);
        $imageUrl = $match[0][0];
        $jpg = \Image::make($imageUrl)->encode('jpg');
        $filename = substr($imageUrl, strrpos($imageUrl, '/'));
        $filename = str_replace('/', '', $filename);
        $media = MediaUploader::fromString($jpg)->useFilename($filename)->upload();
        $product->attachMedia($media, config('constants.media_tags'));

        return true;
    }

    public function delete(Request $request): JsonResponse
    {
        $messageId = $request->get('id', 0);

        if ($messageId) {
            $chatMessage = ChatMessage::where('id', $messageId)->first();
            if ($chatMessage) {
                $chatMessage->delete();
                SuggestedProductList::where('chat_message_id', $messageId)->delete();
            }
        }

        return response()->json(['code' => 200]);
    }

    public function autoCompleteMessages(Request $request): JsonResponse
    {
        $data = AutoCompleteMessage::where('message', 'like', ''.$request->keyword.'%')->pluck('message')->toArray();

        return response()->json(['data' => $data]);
    }

    public function sendemail($message, $model_id, $model_class, $toemail, $chat_id = 0, $subject = null)
    {
        $botReply = ChatbotReply::where('chat_id', $message->id)->get();

        $from_address = config('env.MAIL_FROM_ADDRESS');
        $cc = '';
        //$subject = null;
        $email_id = 0;
        $m = ChatMessage::where('id', $chat_id)->first();
        if ($m) {
            if ($m->from_email != '') {
                $from_address = $m->from_email;
            }

            if ($m->to_email != '') {
                $toemail = $m->to_email;
            }

            if ($m->cc_email != '') {
                $cc = $m->cc_email;
            }

            if ($m->email_id != '') {
                $email_id = $m->email_id;
            }
        }

        $message_body = $message->message;

        if ($email_id > 0) {
            $email = Email::where('id', $message->email_id)->first();
            if ($email) {
                $subject = $email->subject;
                $toemail = $email->from;
            }
        }

        $email = Email::create([
            'model_id' => $model_id,
            'model_type' => $model_class,
            'from' => $from_address ?? '',
            'to' => $toemail,
            'subject' => $subject,
            'message' => $message_body,
            'template' => 'customer-simple',
            'additional_data' => $model_id,
            'status' => 'pre-send',
            'store_website_id' => null,
            'cc' => $cc,
            'is_draft' => 1,
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');

    }

    public function webhookOfficial(Request $request)
    {
        $entries = $request['entry'];
        foreach ($entries as $entry) {
            foreach ($entry['changes'] as $change) {
                $businessAccount = WhatsappBusinessAccounts::where('business_phone_number_id', $change['value']['metadata']['phone_number_id'])->first();
                if (isset($change['messages'])) {
                    foreach ($change['messages'] as $message) {
                        $supplier = $this->findSupplierByNumber($message['from']);
                        $vendor = $this->findVendorByNumber($message['from']);
                        $user = $this->findUserByNumber($message['from']);
                        $dubbizle = $this->findDubbizleByNumber($message['from']);
                        $contact = $this->findContactByNumber($message['from']);
                        $customer = $this->findCustomerByNumber($message['from']);
                        $params = [
                            'unique_id' => $message['id'],
                            'number' => $message['from'],
                            'message_type' => $message['type'],
                            'message' => isset($message['text']) ? $message['text']['body'] : '',
                            'user_id' => $user != null ? $user->id : null,
                            'contact_id' => $contact != null ? $contact->id : null,
                            'supplier_id' => $supplier != null ? $supplier->id : null,
                            'vendor_id' => $vendor != null ? $vendor->id : null,
                            'dubbizle_id' => $dubbizle != null ? $dubbizle->id : null,
                            'customer_id' => $customer != null ? $customer->id : null,
                            'account_id' => $businessAccount->id,
                        ];
                        ChatMessage::create($params);
                    }
                }
                if (isset($change['statuses'])) {
                    foreach ($change['statuses'] as $status) {
                        $chatMessage = ChatMessage::where('unique_id', $status['id'])->first();
                        if ($chatMessage) {
                            if ($status['status'] === 'sent') {
                                $chatMessage->sent = true;
                                $chatMessage->save();
                            }
                            if ($status['status'] === 'delivered') {
                                $chatMessage->is_delivered = true;
                                $chatMessage->save();
                            }
                            if ($status['status'] === 'read') {
                                $chatMessage->is_read = true;
                                $chatMessage->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public function webhookOfficialVerify(Request $request): \Illuminate\Http\Response
    {
        $verifyToken = 'w59YnmcB4w1tzfxVYlPP';
        $mode = $request->get('hub.mode');
        $token = $request->get('hub.verify_token');
        $challenge = $request->get('hub.challenge');

        // Check if a token and mode were sent
        if ($mode && $token) {
            // Check the mode and token sent are correct
            if ($mode === 'subscribe' && $token === $verifyToken) {
                // Respond with 200 OK and challenge token from the request
                return response()->setStatusCode(200)->setContent($challenge)->send();
            } else {
                // Responds with '403 Forbidden' if verify tokens do not match
                return response()->setStatusCode(403);
            }
        }

        return response()->setStatusCode(403);
    }
}
