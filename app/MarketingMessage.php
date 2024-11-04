<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarketingMessage extends Model
{
    protected $fillable =
    [
        'title',
        'message_group_id',
        'scheduled_at',
        'is_sent',
    ];
}
