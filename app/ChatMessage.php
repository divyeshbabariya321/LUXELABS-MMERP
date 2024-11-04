<?php

namespace App;

use App\Http\Controllers\CustomerController;
use App\Models\SocialComments;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Plank\Mediable\Mediable;
use App\Marketing\WhatsappConfig;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatMessage extends Model
{
    // this is guessing status since it is not declared anywhere so
    const MESSAGE_STATUS = [
        '11' => 'Watson Reply',
        '5' => 'Read',
        '0' => 'Unread',
        '12' => 'Suggested Images',
    ];

    // auto reply including chatbot as well
    const AUTO_REPLY_CHAT = [
        7, 8, 9, 10, 11,
    ];

    const EXECLUDE_AUTO_CHAT = [
        7, 8, 9, 10,
    ];

    const CHAT_AUTO_BROADCAST = 8;

    const CHAT_AUTO_WATSON_REPLY = 11;

    const CHAT_SUGGESTED_IMAGES = 12;

    const CHAT_MESSAGE_APPROVED = 2;

    const ERROR_STATUS_SUCCESS = 0;

    const ERROR_STATUS_ERROR = 1;

    const ELASTIC_INDEX = 'messages';

    const LOG_LINE_STRING = ' line ';

    const LOG_FILE_STRING = '(file ';

    use Mediable;

    /**
     * @var string
     *
     * @SWG\Property(property="is_queue",type="boolean")
     * @SWG\Property(property="unique_id",type="integer")
     * @SWG\Property(property="lead_id",type="integer")
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="supplier_id",type="integer")
     * @SWG\Property(property="ticket_id",type="integer")
     * @SWG\Property(property="task_id",type="integer")
     * @SWG\Property(property="erp_user",type="string")
     * @SWG\Property(property="assigned_to",type="string")
     * @SWG\Property(property="contact_id",type="integer")
     * @SWG\Property(property="dubbizle_id",type="integer")
     * @SWG\Property(property="is_reminder",type="boolean")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="issue_id",type="integer")
     * @SWG\Property(property="developer_task_id",type="integer")
     * @SWG\Property(property="lawyer_id",type="integer")
     * @SWG\Property(property="case_id",type="integer")
     * @SWG\Property(property="blogger_id",type="integer")
     * @SWG\Property(property="voucher_id",type="integer")
     * @SWG\Property(property="document_id",type="integer")
     * @SWG\Property(property="payment_receipt_id",type="integer")
     * @SWG\Property(property="group_id",type="integer")
     * @SWG\Property(property="old_id",type="integer")
     * @SWG\Property(property="message_application_id",type="integer")
     * @SWG\Property(property="is_chatbot",type="boolean")
     * @SWG\Property(property="sent_to_user_id",type="integer")
     * @SWG\Property(property="site_development_id",type="integer")
     * @SWG\Property(property="social_strategy_id",type="integer")
     * @SWG\Property(property="store_social_content_id",type="integer")
     * @SWG\Property(property="quoted_message_id",type="integer")
     * @SWG\Property(property="is_reviewed",type="boolean")
     * @SWG\Property(property="hubstaff_activity_summary_id",type="integer")
     * @SWG\Property(property="question_id",type="integer")
     */

    //Purpose - Add learning_id - DEVTASK-4020
    //Purpose : Add additional_data - DEVATSK-4236

    protected $fillable = ['is_queue', 'unique_id', 'bug_id', 'test_case_id', 'test_suites_id', 'lead_id', 'order_id', 'customer_id', 'supplier_id', 'vendor_id', 'charity_id', 'user_id', 'ticket_id', 'task_id', 'erp_user', 'contact_id', 'dubbizle_id', 'assigned_to', 'purchase_id', 'message', 'media_url', 'number', 'approved', 'status', 'error_status', 'resent', 'is_reminder', 'created_at', 'issue_id', 'developer_task_id', 'lawyer_id', 'case_id', 'blogger_id', 'voucher_id', 'document_id', 'group_id', 'old_id', 'message_application_id', 'is_chatbot', 'sent_to_user_id', 'site_development_id', 'social_strategy_id', 'store_social_content_id', 'quoted_message_id', 'is_reviewed', 'hubstaff_activity_summary_id', 'question_id', 'is_email', 'payment_receipt_id', 'learning_id', 'additional_data', 'hubstuff_activity_user_id', 'user_feedback_id', 'user_feedback_category_id', 'user_feedback_status', 'send_by', 'sop_user_id', 'message_en', 'from_email', 'to_email', 'email_id', 'scheduled_at', 'broadcast_numbers_id', 'flow_exit', 'task_time_reminder', 'order_status', 'ui_check_id1', 'time_doctor_activity_summary_id', 'time_doctor_activity_user_id', 'message_type', 'is_audio', 'is_auto_simulator', 'send_by_simulator', 'message_score', 'message_type_id'];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
        ];
    }

    /**
     * Send WhatsApp message via Chat-Api
     *
     * @param  null  $whatsAppNumber
     * @param  null  $message
     * @param  null  $file
     * @param  mixed  $number
     * @return bool|mixed
     */
    public static function sendWithChatApi($number, $whatsAppNumber = null, $message = null, $file = null)
    {
        // Get configs
        $config = WhatsappConfig::getWhatsappConfigs();

        // Set instanceId and token
        if (isset($config[$whatsAppNumber])) {
            $instanceId = $config[$whatsAppNumber]['instance_id'];
            $token = $config[$whatsAppNumber]['token'];
        } else {
            $instanceId = $config[0]['instance_id'];
            $token = $config[0]['token'];
        }

        // Add plus to number and add to array
        $chatApiArray = [
            'phone' => '+'.$number,
        ];

        if ($message != null && $file == null) {
            $chatApiArray['body'] = $message;
            $link = 'sendMessage';
        } else {
            $exploded = explode('/', $file);
            $filename = end($exploded);
            $chatApiArray['body'] = $file;
            $chatApiArray['filename'] = $filename;
            $link = 'sendFile';
            $chatApiArray['caption'] = $message;
        }

        $url = "https://api.chat-api.com/instance$instanceId/$link?token=".$token;

        $response = Http::post($url, $chatApiArray);

        if ($response->failed()) {
            $err = $response->body();
        }

        $responseData = $response->json();

        // Check for errors
        if ($response->failed()) {
            // Log error
            Log::channel('whatsapp')->debug(self::LOG_FILE_STRING.__FILE__.self::LOG_LINE_STRING.__LINE__.') cURL Error for number '.$number.':'.$err);

            return false;
        } else {
            // Log curl response
            Log::channel('chatapi')->debug('cUrl:'.$responseData."\nMessage: ".$message."\nFile:".$file."\n");

            // Check for possible incorrect response
            if (! is_array($responseData) || array_key_exists('sent', $responseData) && ! $responseData['sent']) {
                // Log error
                Log::channel('whatsapp')->debug(self::LOG_FILE_STRING.__FILE__.self::LOG_LINE_STRING.__LINE__.') Something was wrong with the message for number '.$number.': '.$responseData);

                return false;
            } else {
                // Log successful send
                Log::channel('whatsapp')->debug(self::LOG_FILE_STRING.__FILE__.self::LOG_LINE_STRING.__LINE__.') Message was sent to number '.$number.':'.$responseData);
            }
        }

        return $responseData;
    }

    /**
     * Handle Chat-Api ACK-message
     *
     * @param  mixed  $json
     */
    public static function handleChatApiAck($json)
    {
        // Loop over ack
        if (isset($json['ack'])) {
            foreach ($json['ack'] as $chatApiAck) {
                // Find message
                $chatMessage = self::where('unique_id', $chatApiAck['id'])->first();

                // Chat Message found and status is set
                if ($chatMessage && isset($chatApiAck['status'])) {
                    // Set delivered
                    if ($chatApiAck['status'] == 'delivered') {
                        $chatMessage->is_delivered = 1;
                        $chatMessage->save();
                    }

                    // Set views
                    if ($chatApiAck['status'] == 'viewed') {
                        $chatMessage->is_delivered = 1;
                        $chatMessage->is_read = 1;
                        $chatMessage->save();
                    }
                }
            }
        }
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }

    /**
     * Check if the message has received a broadcast price reply
     */
    public function isSentBroadcastPrice(): bool
    {
        // Get count
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', ChatMessage::class)->where('type', 'broadcast-prices')->count();

        // Return true or false
        return $count > 0 ? true : false;
    }

    public static function updatedUnreadMessage($customerId, $status = 0)
    {
        // if reply is not auto reply or the suggested reply from chat then only update status
        if (! empty($status) && ! in_array($status, self::AUTO_REPLY_CHAT)) {
            self::where('customer_id', $customerId)->where('status', 0)->update(['status' => 5]);
        }
    }

    public function taskUser(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chatmsg(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'user_id', 'user_id')->latest();
    }

    //END - DEVTASK-4203

    public function sendTaskUsername()
    {
        $name = '';

        if ($this->erp_user > 0) {
            $taskUser = $this->taskUser;
            if ($taskUser) {
                $name = $taskUser->name;
            }
        }

        return $name;
    }

    public function sendername()
    {
        $name = '';

        if ($this->user_id > 0) {
            $taskUser = $this->user;
            if ($taskUser) {
                $name = $taskUser->name;
            }
        }

        return $name;
    }

    public static function pendingQueueGroupList($params = [])
    {
        return self::where($params)->where('group_id', '>', 0)
            ->pluck('group_id', 'group_id')
            ->toArray();
    }

    public static function pendingQueueLeadList($params = [])
    {
        return self::where($params)->where('lead_id', '>', 0)
            ->pluck('lead_id', 'lead_id')
            ->toArray();
    }

    public static function getQueueLimit()
    {
        $limit = Setting::where('name', 'is_queue_sending_limit')->first();

        return ($limit) ? json_decode($limit->val, true) : [];
    }

    public static function getQueueTime()
    {
        $limit = Setting::where('name', 'is_queue_sending_time')->first();

        return ($limit) ? json_decode($limit->val, true) : [];
    }

    public static function getStartTime()
    {
        $limit = Setting::where('name', 'is_queue_send_start_time')->first();

        return ($limit) ? $limit->val : 0;
    }

    public static function getEndTime()
    {
        $limit = Setting::where('name', 'is_queue_send_end_time')->first();

        return ($limit) ? $limit->val : 0;
    }

    public static function getSupplierForwardTo()
    {
        $no = Setting::where('name', 'supplier_forward_message_no')->first();

        return ($no) ? $no->val : 0;
    }

    public function chatBotReply(): HasOne
    {
        return $this->hasOne("\App\ChatBotReply", 'chat_id', 'id');
    }

    public function chatBotReplychat(): HasOne
    {
        return $this->hasOne(ChatbotReply::class, 'replied_chat_id', 'id');
    }

    public function chatBotReplychatlatest(): HasMany
    {
        return $this->hasMany(ChatbotReply::class, 'replied_chat_id', 'id');
    }

    public function suggestion(): HasOne
    {
        return $this->hasOne(SuggestedProduct::class, 'chat_message_id', 'id');
    }

    public static function getLastImgProductId($customerId)
    {
        return ChatMessage::where('customer_id', $customerId)
            ->whereNull('chat_messages.number')
            ->whereNotIn('status', array_merge(self::AUTO_REPLY_CHAT, [2]))
            ->select(['chat_messages.*'])
            ->orderByDesc('chat_messages.created_at')
            ->first();
    }

    /**
     *  Get information by ids
     *
     * @param []
     * @param  mixed  $ids
     * @param  mixed  $fields
     * @param  mixed  $toArray
     * @return mixed
     */
    public static function getInfoByIds($ids, $fields = ['*'], $toArray = false)
    {
        $list = self::whereIn('id', $ids)->select($fields)->get();

        if ($toArray) {
            $list = $list->toArray();
        }

        return $list;
    }

    /**
     *  Get information by ids
     *
     * @param []
     * @param  mixed  $ids
     * @param  mixed  $toArray
     * @return mixed
     */
    public static function getGroupImagesByIds($ids, $toArray = false)
    {
        $list = Mediables::where('mediable_type', self::class)
            ->whereIn('mediable_id', $ids)
            ->groupBy('mediable_id')
            ->select(['mediable_id', DB::raw('group_concat(media_id) as image_ids')])
            ->get();

        if ($toArray) {
            $list = $list->toArray();
        }

        return $list;
    }

    /**
     *  Get information by ids
     *
     * @param []
     * @param  mixed  $field
     * @param  mixed  $ids
     * @param  mixed  $fields
     * @param  mixed  $params
     * @param  mixed  $toArray
     * @return mixed
     */
    public static function getInfoByObjectIds($field, $ids, $fields = ['*'], $params = [], $toArray = false)
    {
        unset($_GET['page']);
        $list = self::whereIn($field, $ids)->where(function ($q) {
            $q->whereNull('group_id')->orWhere('group_id', 0);
        })->whereNotIn('status', self::EXECLUDE_AUTO_CHAT);

        if (! empty($params['previous']) && $params['previous'] == true && ! empty($params['lastMsg']) && is_numeric($params['lastMsg'])) {
            $list = $list->where('id', '<', $params['lastMsg']);
        }

        if (! empty($params['next']) && $params['next'] == true && ! empty($params['lastMsg'])) {
            $list = $list->where('id', '>', $params['lastMsg']);
        }

        $list = $list->orderByDesc('created_at')->select($fields)->paginate(10);

        if ($toArray) {
            $list = $list->items();
        }

        return $list;
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Check send lead price
     * $customer object
     * customer
     *
     * @param  mixed  $customer
     * @param  mixed  $log_comment
     **/
    public function sendLeadPrice($customer, $log_comment = '')
    {
        $media = $this->getMedia(config('constants.attach_image_tag'))->first();
        if ($media) {
            Log::channel('customer')->info('Media image found for customer id : '.$customer->id);
            $log_comment = $log_comment.' Media image found for customer with ID : '.$customer->id;

            $mediable = Mediables::where('media_id', $media->id)
                ->where('mediable_type', Product::class)

                ->first();
            if (! empty($mediable)) {
                $log_comment = $log_comment.' Mediable found for customer with ID : '.$customer->id;
                Log::channel('customer')->info('Mediable for customer id : '.$customer->id);
                try {
                    app(CustomerController::class)->dispatchBroadSendPrice($customer, array_unique([$mediable->mediable_id]));
                    $log_comment = $log_comment.' Mediable dispatched with ID : '.$mediable->mediable_id;
                } catch (Exception $e) {
                    Log::channel('customer')->info($e->getMessage());
                }
            } else {
                $log_comment = $log_comment.' Mediable not found ';
            }
        } else {
            $log_comment = $log_comment.' Media not found ';
        }
    }

    /**
     * Check send lead dimention
     * $customer object
     * customer
     *
     * @param  mixed  $customer
     **/
    public function sendLeadDimention($customer)
    {
        $media = $this->getMedia(config('constants.attach_image_tag'))->first();
        if ($media) {
            Log::channel('customer')->info('Media image found for customer id : '.$customer->id);

            $mediable = Mediables::where('media_id', $media->id)
                ->where('mediable_type', Product::class)
                ->first();
            if (! empty($mediable)) {
                Log::channel('customer')->info('Mediable for customer id : '.$customer->id);
                try {
                    app(CustomerController::class)->dispatchBroadSendPrice($customer, array_unique([$mediable->mediable_id]), true);
                } catch (Exception $e) {
                    Log::channel('customer')->info($e->getMessage());
                }
            }
        }
    }

    public function getRecieverUsername(): HasOne
    {
        return $this->hasOne(InstagramUsersList::class, 'id', 'instagram_user_id');
    }

    public function getSenderUsername(): HasOne
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }

    public function socialComment()
    {
        return $this->hasOne(SocialComments::class, 'id', 'message_type_id');
    }

    public function socialContact()
    {
        return $this->hasOne(SocialContact::class, 'id', 'message_type_id');
    }
}
