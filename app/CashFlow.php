<?php

namespace App;

use App\Events\CashFlowCreated;
use App\Events\CashFlowUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CashFlow extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="cash_flow_category_id",type="integer")
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="amount",type="integer")
     * @SWG\Property(property="type",type="string")
     * @SWG\Property(property="actual",type="string")
     * @SWG\Property(property="currency",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="order_status",type="sting")
     * @SWG\Property(property="updated_by",type="integer")
     * @SWG\Property(property="cash_flow_able_id",type="integer")
     * @SWG\Property(property="cash_flow_able_type",type="sting")
     */
    protected $fillable = [
        'user_id', 'cash_flow_category_id', 'description', 'date', 'amount', 'erp_amount', 'erp_eur_amount', 'amount_eur', 'type', 'expected', 'actual', 'currency', 'status', 'order_status', 'updated_by', 'cash_flow_able_id', 'cash_flow_able_type', 'monetary_account_id',
    ];

    protected $dispatchesEvents = [
        'created' => CashFlowCreated::class,
        'updated' => CashFlowUpdated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'model_id')->where('model_type', CashFlow::class);
    }

    public function website(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'cash_flow_able_id');
    }

    public function cashFlowAble(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'cash_flow_able_id');
    }

    public function assetsManager(): HasOne
    {
        return $this->hasOne(AssetsManager::class, 'id', 'cash_flow_able_id');
    }

    public function paymentReceipt(): HasOne
    {
        return $this->hasOne(PaymentReceipt::class, 'id', 'cash_flow_able_id');
    }

    public function monetaryAccount(): HasOne
    {
        return $this->hasOne(MonetaryAccount::class, 'id', 'monetary_account_id');
    }

    public function getLink()
    {
        if ($this->cash_flow_able_type == Order::class) {
            return '<a href="'.route('order.show', $this->cash_flow_able_id).'" class="btn-link">'.$this->cash_flow_able_id.'</a>';
        } elseif ($this->cash_flow_able_type == PaymentReceipt::class) {
            return '<a href="/voucher" class="btn-link">'.$this->cash_flow_able_id.'</a>';
        } else {
            return '<a href="javascript:;" class="btn-link">'.$this->cash_flow_able_id.'</a>';
        }
    }

    public function get_bname()
    {
        if ($this->cash_flow_able_type == Order::class) {
            return ($this->order) ? $this->order->customer->name : 'N/A';
        } elseif ($this->cash_flow_able_type == AssetsManager::class) {
            return ($this->assetsManager) ? $this->assetsManager->name : 'N/A';
        } elseif ($this->cash_flow_able_type == PaymentReceipt::class) {
            return ($this->user) ? $this->user->name : 'N/A';
        } elseif ($this->cash_flow_able_type == HubstaffActivityByPaymentFrequency::class) {
            return ($this->user) ? $this->user->name : 'N/A';
        } else {
            return 'Cash';
        }
    }
}
