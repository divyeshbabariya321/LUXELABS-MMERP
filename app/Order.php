<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Events\OrderCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\CommunicationHistory;
use App\Invoice;
use App\Waybill;
use App\DeliveryApproval;
use App\ChatMessage;
use App\OrderReport;
use App\Message;
use App\Customer;
use App\StatusChange;
use App\OrderCustomerAddress;
use App\WebsiteStore;
use App\OrderStatus;
use App\StoreWebsiteOrder;

class Order extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="order_type",type="string")
     * @SWG\Property(property="order_date",type="datetime")
     * @SWG\Property(property="awb",type="string")
     * @SWG\Property(property="client_name",type="sting")
     * @SWG\Property(property="city",type="sting")
     * @SWG\Property(property="contact_detail",type="sting")
     * @SWG\Property(property="shoe_size",type="sting")
     * @SWG\Property(property="clothing_size",type="sting")
     * @SWG\Property(property="solophone",type="sting")
     * @SWG\Property(property="advance_detail",type="sting")
     * @SWG\Property(property="advance_date",type="datetime")
     * @SWG\Property(property="balance_amount",type="float")
     * @SWG\Property(property="office_phone_number",type="integer")
     * @SWG\Property(property="order_status",type="sting")
     * @SWG\Property(property="order_status_id",type="integer")
     * @SWG\Property(property="estimated_delivery_date",type="datetime")
     * @SWG\Property(property="note_if_any",type="sting")
     * @SWG\Property(property="date_of_delivery",type="datetime")
     * @SWG\Property(property="received_by",type="integer")
     * @SWG\Property(property="payment_mode",type="string")
     * @SWG\Property(property="auto_messaged",type="string")
     * @SWG\Property(property="auto_messaged_date",type="datetime")
     * @SWG\Property(property="auto_emailed",type="string")
     * @SWG\Property(property="auto_emailed_date",type="datetime")
     * @SWG\Property(property="remark",type="string")
     * @SWG\Property(property="whatsapp_number",type="string")
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="is_priority",type="boolean")
     * @SWG\Property(property="currency",type="string")
     * @SWG\Property(property="invoice_id",type="string")
     */
    use SoftDeletes;

    const ORDER_STATUS_TEMPLATE = 'Greetings from Solo Luxury Ref: order number #{order_id} we have updated your order with status : #{order_status}.';

    protected $fillable = [
        'order_id',
        'customer_id',
        'order_type',
        'order_date',
        'awb',
        'client_name',
        'city',
        'contact_detail',
        'shoe_size',
        'clothing_size',
        'solophone',
        'advance_detail',
        'advance_date',
        'balance_amount',
        'sales_person',
        'office_phone_number',
        'order_status',
        'order_status_id',
        'estimated_delivery_date',
        'note_if_any',
        'date_of_delivery',
        'received_by',
        'payment_mode',
        'auto_messaged',
        'auto_messaged_date',
        'auto_emailed',
        'auto_emailed_date',
        'remark',
        'whatsapp_number',
        'user_id',
        'is_priority',
        'currency',
        'invoice_id',
        'store_currency_code',
        'monetary_account_id',
        'website_address_id',
        'transaction_id',
        'order_magento_id',
        'order_return_request',
        'purchase_product_status_id',
    ];

    protected $appends = ['action'];

    public function order_product(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id')->where('product_id', '!=', 0);
    }

    public function orderStatusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, OrderProduct::class, 'user_id', 'role_id');
    }

    public function latest_product(): HasOne
    {
        return $this->hasOne(OrderProduct::class, 'order_id', 'id')->latest();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function Comment(): HasMany
    {
        return $this->hasMany(Comment::class, 'subject_id', 'id')
            ->where('subject_type', '=', Order::class);
    }

    public function messages(): HasMany | HasOne
    {
        return $this->hasOne(Message::class, 'moduleid')->where('moduletype', 'order')->latest();
    }

    public function reports(): HasMany | HasOne
    {
        return $this->hasOne(OrderReport::class, 'order_id')->latest();
    }

    public function latest_report(): HasOne
    {
        return $this->hasOne(OrderReport::class, 'order_id')->latest();
    }

    public function status_changes(): HasMany
    {
        return $this->hasMany(StatusChange::class, 'model_id')->where('model_type', Order::class)->latest();
    }

    public function many_reports(): HasMany
    {
        return $this->hasMany(OrderReport::class, 'order_id')->latest();
    }

    public function whatsapps(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'order_id')->latest()->first();
    }

    public function delivery_approval(): HasOne
    {
        return $this->hasOne(DeliveryApproval::class);
    }

    public function waybill(): HasOne
    {
        return $this->hasOne(Waybill::class);
    }

    public function waybills(): HasMany
    {
        return $this->hasMany(Waybill::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function is_sent_initial_advance()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Order::class)->where('type', 'initial-advance')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_advance_receipt()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Order::class)->where('type', 'advance-receipt')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_online_confirmation()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Order::class)->where('type', 'online-confirmation')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_refund_initiated()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Order::class)->where('type', 'refund-initiated')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_offline_confirmation()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Order::class)->where('type', 'offline-confirmation')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_order_delivered()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Order::class)->where('type', 'order-delivered')->count();

        return $count > 0 ? true : false;
    }

    public function order_status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function getActionAttribute()
    {
        return $this->reports();
    }

    protected $dispatchesEvents = [
        'created' => OrderCreated::class,
    ];

    public function cashFlows(): MorphMany
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }

    public function storeWebsiteOrder(): HasOne
    {
        return $this->hasOne(StoreWebsiteOrder::class, 'order_id', 'id');
    }

    public function whatsappAll($needBroadcast = false): HasMany
    {
        if ($needBroadcast) {
            return $this->hasMany(ChatMessage::class, 'order_id')->where(function ($q) {
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere('group_id', '>', 0);
            })->latest();
        } else {
            return $this->hasMany(ChatMessage::class, 'order_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }

    public function status(): HasOne
    {
        return $this->hasOne(OrderStatus::class, 'id', 'order_status_id');
    }

    public function orderCustomerAddress(): HasMany
    {
        return $this->hasMany(OrderCustomerAddress::class, 'order_id', 'id');
    }

    public function shippingAddress()
    {
        return $this->orderCustomerAddress()->where('address_type', 'shipping')->first();
    }

    public function billingAddress()
    {
        return $this->orderCustomerAddress()->where('address_type', 'billing')->first();
    }

    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class, 'id', 'model_id');
    }

    public function duty_tax(): HasOne
    {
        return $this->hasOne(WebsiteStore::class, 'website_id', 'store_id');
    }

    public function getWebsiteTitle()
    {
        $storeWebsiteOrder = $this->storeWebsiteOrder;

        if ($storeWebsiteOrder) {
            $website = $storeWebsiteOrder->storeWebsite;
            if ($website) {
                return $website->title;
            }
        }

        return false;
    }

    public function totalWayBills()
    {
        $waybills = $this->waybills;
        $awbno    = [];
        if (! $waybills->isEmpty()) {
            foreach ($waybills as $waybill) {
                $awbno[] = $waybill->awb;
            }
        }

        return implode(',', $awbno);
    }

    public function getLatestChatMessage()
    {
        return ChatMessage::where('order_id', $this->id)->where('chat_messages.message','!=', '')->orderBy("created_at", "DESC")->first();
    }
}
