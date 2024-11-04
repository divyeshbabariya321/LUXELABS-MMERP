<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ReturnExchangeProduct;
use App\ReturnExchangeHistory;
use App\ReturnExchangeStatus;
use App\Customer;

class ReturnExchange extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="type",type="string")
     * @SWG\Property(property="reason_for_refund",type="string")
     * @SWG\Property(property="refund_amount",type="float")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="pickup_address",type="string")
     * @SWG\Property(property="refund_amount_mode",type="string")
     * @SWG\Property(property="chq_number",type="string")
     * @SWG\Property(property="reawbmarks",type="string")
     * @SWG\Property(property="payment",type="string")
     * @SWG\Property(property="remarks",type="string")
     * @SWG\Property(property="date_of_refund",type="datetime")
     * @SWG\Property(property="date_of_issue",type="datetime")
     * @SWG\Property(property="dispatch_date",type="datetime")
     * @SWG\Property(property="date_of_request",type="datetime")
     * @SWG\Property(property="details",type="string")
     * @SWG\Property(property="credited",type="string")
     * @SWG\Property(property="est_completion_date",type="datetime")
     */
    use Mediable;

    use SoftDeletes;

    protected $fillable = [
        'id',
        'customer_id',
        'type',
        'reason_for_refund',
        'refund_amount',
        'status',
        'pickup_address',
        'remarks',
        'refund_amount_mode',
        'chq_number',
        'awb',
        'payment',
        'date_of_refund',
        'date_of_issue',
        'details',
        'dispatch_date',
        'date_of_request',
        'credited',
        'est_completion_date',
        'send_email',
        'website_id',
    ];

    const STATUS = [
        1 => 'Return request received from customer',
        2 => 'Return request sent to courier',
        3 => 'Return pickup',
        4 => 'Return received in warehouse',
        5 => 'Return accepted',
        6 => 'Return rejected',
    ];

    const ORDER_EXCHANGE_STATUS_TEMPLATE = 'Greetings from Solo Luxury Ref: Exchange number #{id} we have updated your Exchange with status : #{status}.';

    public function notifyToUser()
    {
        if ($this->type == 'refund') {
        }
    }

    public function returnExchangeProducts(): HasMany
    {
        return $this->hasMany(ReturnExchangeProduct::class, 'return_exchange_id', 'id');
    }

    public function returnExchangeHistory(): HasMany
    {
        return $this->hasMany(ReturnExchangeHistory::class, 'return_exchange_id', 'id');
    }

    public function returnExchangeStatus(): HasOne
    {
        return $this->hasOne(ReturnExchangeStatus::class, 'id', 'status');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Update return exchange history
     */
    public function updateHistory()
    {
        ReturnExchangeHistory::create([
            'return_exchange_id' => $this->id,
            'status_id'          => $this->status,
            'user_id'            => Auth::user()->id,
            'comment'            => $this->remarks,
            'history_type'       => 'status',
        ]);

        return true;
    }

    public function cashFlows(): MorphMany
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }
}
