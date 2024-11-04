<?php

namespace App\Models\Twilio;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Customer;

class TwilioMessageDeliveryLogs extends Model
{
    protected $fillable = ['marketing_message_customer_id', 'customer_id', 'account_sid', 'message_sid', 'to', 'from', 'delivery_status', 'api_version'];

    public function customers(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}
