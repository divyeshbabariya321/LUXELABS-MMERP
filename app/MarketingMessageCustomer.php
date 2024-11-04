<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarketingMessageCustomer extends Model
{
    protected $fillable =
    [
        'marketing_message_id',
        'customer_id',
        'is_sent',
    ];
}
