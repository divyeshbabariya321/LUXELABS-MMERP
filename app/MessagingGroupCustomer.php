<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessagingGroupCustomer extends Model
{
    protected $fillable =
    [
        'message_group_id',
        'customer_id',
    ];
}
