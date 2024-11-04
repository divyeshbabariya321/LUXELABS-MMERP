<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Plank\Mediable\Mediable;
use App\Events\PaymentReceiptCreated;
use App\Events\PaymentReceiptUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\User;
use App\ChatMessage;


class PaymentReceipt extends Model
{
    use HasFactory;

    /**
     * @var string
     *
     * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="payment_method_id",type="integer")
     * @SWG\Property(property="worked_minutes",type="integer")
     * @SWG\Property(property="payment",type="float")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="task_id",type="interger")
     * @SWG\Property(property="developer_task_id",type="interger")
     * @SWG\Property(property="user_id",type="interger")
     * @SWG\Property(property="rate_estimated",type="string")
     * @SWG\Property(property="remarks",type="string")
     * @SWG\Property(property="currency",type="string")
     * @SWG\Property(property="billing_start_date",type="datetime")
     * @SWG\Property(property="billing_end_date",type="datetime")
     * @SWG\Property(property="billing_due_date",type="datetime")
     */
    use Mediable;

    protected $fillable = ['date', 'worked_minutes', 'payment', 'status', 'task_id', 'developer_task_id', 'user_id', 'rate_estimated', 'remarks', 'currency', 'billing_start_date', 'billing_end_date', 'billing_due_date', 'by_command'];

    protected $dispatchesEvents = [
        'created' => PaymentReceiptCreated::class,
        'updated' => PaymentReceiptUpdated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chat_messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderByDesc('id');
    }

    public function whatsappAll($needBroadcast = false): HasMany
    {
        if ($needBroadcast) {
            return $this->hasMany(ChatMessage::class, 'payment_receipt_id')->where(function ($q) {
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere('group_id', '>', 0);
            })->latest();
        } else {
            return $this->hasMany(ChatMessage::class, 'payment_receipt_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }

    public function cashFlows(): MorphMany
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }

    public function saveWithoutEvents(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }
}
