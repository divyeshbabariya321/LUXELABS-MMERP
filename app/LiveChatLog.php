<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveChatLog extends Model
{

    protected $fillable = ['id', 'customer_id', 'thread', 'log'];
}
