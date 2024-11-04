<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveChatEventLog extends Model
{

    protected $fillable = ['id', 'customer_id', 'thread', 'event_type', 'log', 'store_website_id'];
}
