<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Order;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class OrderEmailSendJourneyLog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="id",type="integer")
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="steps",type="string")
     * @SWG\Property(property="model_type",type="string")
     * @SWG\Property(property="send_type",type="string")
     * @SWG\Property(property="seen",type="string")
     * @SWG\Property(property="from_email",type="string")
     * @SWG\Property(property="to_email",type="string")
     * @SWG\Property(property="subject",type="string")
     * @SWG\Property(property="message",type="string")
     * @SWG\Property(property="template",type="text")
     * @SWG\Property(property="error_msg",type="text")
     * @SWG\Property(property="created_at",type="date")
     */
    protected $fillable = ['id', 'order_id', 'steps', 'model_type', 'send_type', 'seen', 'from_email', 'to_email', 'subject', 'message', 'template', 'error_msg', 'created_at'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
