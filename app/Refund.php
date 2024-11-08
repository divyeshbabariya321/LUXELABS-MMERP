<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Events\RefundCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Customer;
use App\Order;
class Refund extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="type",type="string")
     * @SWG\Property(property="chq_number",type="string")
     * @SWG\Property(property="date_of_refund",type="datetime")
     * @SWG\Property(property="date_of_issue",type="datetime")
     * @SWG\Property(property="dispatch_date",type="datetime")
     * @SWG\Property(property="date_of_request",type="datetime")
     * @SWG\Property(property="details",type="string")
     * @SWG\Property(property="credited",type="string")
     */
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'order_id', 'type', 'chq_number', 'awb', 'date_of_refund', 'date_of_issue', 'details', 'dispatch_date', 'date_of_request', 'credited',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    protected $dispatchesEvents = [
        'created' => RefundCreated::class,
    ];

    public function cashFlows(): MorphMany
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }
}
