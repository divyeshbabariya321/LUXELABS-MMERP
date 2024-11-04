<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="phone",type="string")
     * @SWG\Property(property="city",type="string")
     * @SWG\Property(property="whatsapp_number",type="integer")
     * @SWG\Property(property="chat_session_id",type="integer")
     * @SWG\Property(property="in_w_list",type="string")

     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")

     * @SWG\Property(property="reminder_from",type="sting")
     * @SWG\Property(property="reminder_last_reply",type="sting")
     * @SWG\Property(property="wedding_anniversery",type="sting")
     * @SWG\Property(property="dob",type="datetime")
     * @SWG\Property(property="do_not_disturb",type="sting")
     */
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'city',
        'whatsapp_number',
        'chat_session_id',
        'in_w_list',
        'store_website_id',
        'user_id',
        'reminder_from',
        'reminder_last_reply',
        'wedding_anniversery',
        'dob',
        'do_not_disturb',
        'store_name',
        'language',
        'newsletter',
        'platform_id',
        'priority',
    ];

    protected static function boot()
    {
        parent::boot();
        self::updating(function ($model) {
            if (! empty(Auth::id())) {
                $model->updated_by = Auth::id();
            }
        });
        self::saving(function ($model) {
            if (! empty(Auth::id())) {
                $model->updated_by = Auth::id();
            }
        });
        self::creating(function ($model) {
            if (! empty(Auth::id())) {
                $model->updated_by = Auth::id();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'notes' => 'array',
        ];
    }

    public function leads(): HasMany
    {
        return $this->hasMany(ErpLeads::class)->orderByDesc('created_at');
    }

    public function customerAddress(): HasMany
    {
        return $this->hasMany(CustomerAddressData::class, 'customer_id', 'id');
    }

    public function dnd(): HasMany
    {
        return $this->hasMany(CustomerBulkMessageDND::class, 'customer_id', 'id')->where('filter', app('request')->keyword_filter);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderByDesc('created_at');
    }

    public function latestOrder(): HasMany
    {
        return $this->hasMany(Order::class)->orderByDesc('created_at')->first();
    }

    public function latestRefund(): HasMany
    {
        return $this->hasMany(ReturnExchange::class)->orderByDesc('created_at')->first();
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(ReturnExchange::class)->orderByDesc('created_at');
    }

    public function suggestion(): HasOne
    {
        return $this->hasOne(SuggestedProduct::class);
    }

    public function instructions(): HasMany
    {
        return $this->hasMany(Instruction::class);
    }

    public function private_views(): HasMany
    {
        return $this->hasMany(PrivateView::class);
    }

    public function latest_order(): HasMany
    {
        return $this->hasMany(Order::class)->latest()->first();
    }

    public function many_reports(): HasMany
    {
        return $this->hasMany(OrderReport::class, 'customer_id')->latest();
    }

    public function allMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'customer_id', 'id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'customer_id')->latest()->first();
    }

    public function messages_all(): HasMany
    {
        return $this->hasMany(Message::class, 'customer_id')->latest();
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'model_id')->where('model_type', Customer::class);
    }

    public function whatsapps(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'customer_id')->where('status', '!=', '7')->latest()->first();
    }

    public function call_recordings(): HasMany
    {
        return $this->hasMany(CallRecording::class, 'customer_id')->latest();
    }

    public function whatsapps_all(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'customer_id')->whereNotIn('status', ['7', '8', '9'])->latest();
    }

    public function messageHistory($count = 3): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'customer_id')->whereNotIn('status', ['7', '8', '9', '10'])->take($count)->latest();
    }

    public function bulkMessagesKeywords(): BelongsToMany
    {
        return $this->belongsToMany(BulkCustomerRepliesKeyword::class, 'bulk_customer_replies_keyword_customer', 'customer_id', 'keyword_id');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'customer_id')->whereNotIn('status', ['7', '8', '9'])->latest()->first();
    }

    public function credits_issued(): HasMany
    {
        return $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Customer::class)->where('type', 'issue-credit')->where('method', 'email');
    }

    public function instagramThread(): HasOne
    {
        return $this->hasOne(InstagramThread::class);
    }

    public function is_initiated_followup()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Customer::class)->where('type', 'initiate-followup')->where('is_stopped', 0)->count();

        return $count > 0 ? true : false;
    }

    public function whatsappAll($needBroadcast = false): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'customer_id')
            ->when($needBroadcast, function ($query) {
                $query->where(function ($q) {
                    $q->whereIn('status', ['7', '8', '9', '10'])
                        ->orWhere('group_id', '>', 0);
                });
            }, function ($query) {
                $query->whereNotIn('status', ['7', '8', '9', '10']);
            })
            ->latest();
    }

    public function whatsapp_number_change_notified()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Customer::class)->where('type', 'number-change')->count();

        return $count > 0 ? true : false;
    }

    public function getCommunicationAttribute()
    {
        $message = $this->messages();
        $whatsapp = $this->whatsapps();

        if ($message && $whatsapp) {
            if ($message->created_at > $whatsapp->created_at) {
                return $message;
            }

            return $whatsapp;
        }

        if ($message) {
            return $message;
        }

        return $whatsapp;
    }

    public function getLeadAttribute()
    {
        return $this->leads()->latest()->first();
    }

    public function getOrderAttribute()
    {
        return $this->orders()->latest()->first();
    }

    public function facebookMessages(): HasMany
    {
        return $this->hasMany(FacebookMessages::class);
    }

    public function broadcastLatest(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'customer_id', 'id')->where('status', '8')->where('group_id', '>', 0)->latest();
    }

    public function customerMarketingPlatformRemark(): HasMany
    {
        return $this->hasMany(CustomerMarketingPlatform::class, 'customer_id', 'id')->whereNotNull('remark')->orderByDesc('created_at');
    }

    public function customerMarketingPlatformActive(): HasOne
    {
        return $this->hasOne(CustomerMarketingPlatform::class, 'customer_id', 'id')->whereNull('remark');
    }

    public function broadcastAll(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'customer_id', 'id')->where('status', '8')->where('group_id', '>', 0)->orderByDesc('id');
    }

    public function lastBroadcastSend(): HasOne
    {
        return $this->hasOne(ImQueue::class, 'number_to', 'phone')->whereNotNull('sent_at')->latest();
    }

    public function lastImQueueSend(): HasOne
    {
        return $this->hasOne(ImQueue::class, 'number_to', 'phone')->orderByDesc('sent_at');
    }

    public function notDelieveredImQueueMessage(): HasOne
    {
        return $this->hasOne(ImQueue::class, 'number_to', 'phone')->where('sent_at', '2002-02-02 02:02:02')->latest();
    }

    public function receivedLastestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'customer_id', 'id')->whereNotNull('number')->latest();
    }

    public function hasDND()
    {
        return ($this->do_not_disturb == 1) ? true : false;
    }

    public function kyc(): HasMany
    {
        return $this->hasMany(CustomerKycDocument::class, 'customer_id', 'id');
    }

    public function lastMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'customer_id')->where('message_application_id', 2)->orderBy('id', 'desc');
    }

    public function usedCredit(): HasMany
    {
        return $this->hasMany(CreditHistory::class, 'customer_id')->where('type', 'MINUS');
    }

    public function creditIn(): HasMany
    {
        return $this->hasMany(CreditHistory::class, 'customer_id')->where('type', 'ADD');
    }

    /**
     *  Get information by ids
     *
     *  @param []
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
     * Get store website detail
     */
    public function storeWebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id');
    }

    public function return_exchanges(): HasMany
    {
        return $this->hasMany(ReturnExchange::class, 'customer_id');
    }

    public static function ListSource()
    {
        return [
            'instagram' => 'Instagram',
            'default' => 'Default',
        ];
    }

    public function wishListBasket(): HasOne
    {
        return $this->hasOne(CustomerBasket::class, 'customer_id', 'id');
    }

    public function maillistCustomerHistory(): HasOne
    {
        return $this->hasOne(MaillistCustomerHistory::class, 'customer_id', 'id');
    }

    public function getOrderById($id)
    {
        return $this->orders()->where('id', $id)->first();
    }

    public function getRefundById($id)
    {
        return $this->refunds()->where('id', $id)->first();
    }

    public function getDuplicateCustomers()
    {
        return self::where('phone', $this->phone)->where('id', '!=', $this->id)->get();
    }
}
