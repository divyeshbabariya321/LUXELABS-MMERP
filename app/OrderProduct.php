<?php

namespace App;
use App\StatusChange;
use App\Purchase;
use App\Product;
use App\PrivateView;
use App\Order;
use App\Message;
use App\CommunicationHistory;
use App\ChatMessage;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="sku",type="string")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="product_price",type="float")
     * @SWG\Property(property="size",type="string")
     * @SWG\Property(property="color",type="string")
     * @SWG\Property(property="qty",type="integer")
     * @SWG\Property(property="purchase_status",type="string")
     * @SWG\Property(property="supplier_discount_info_id",type="integer")
     * @SWG\Property(property="inventory_status_id",type="integer")
     */
    protected $fillable = [
        'order_id',
        'sku',
        'product_id',
        'product_price',
        'currency',
        'eur_price',
        'size',
        'color',
        'qty',
        'purchase_status',
        'supplier_discount_info_id',
        'inventory_status_id',
    ];

    protected $appends = ['communication'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function order_product_details(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->select('id', 'name');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function private_view(): HasOne
    {
        return $this->hasOne(PrivateView::class);
    }

    public function is_delivery_date_changed()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', OrderProduct::class)->where('type', 'order-delivery-date-changed')->count();

        return $count > 0 ? true : false;
    }

    public function messages(): HasMany | Null
    {
        return $this->hasMany(Message::class, 'moduleid', 'order_id')->where('moduletype', 'order')->latest()->first();
    }

    public function whatsapps(): HasMany | Null
    {
        return $this->hasMany(ChatMessage::class, 'order_id', 'order_id')->latest()->first();
    }

    public function status_changes(): HasMany
    {
        return $this->hasMany(StatusChange::class, 'model_id')->where('model_type', OrderProduct::class)->latest();
    }

    public function getCommunicationAttribute()
    {
        $message  = $this->messages();
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
}
