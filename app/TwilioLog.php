<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioLog extends Model
{
    protected $fillable = [
        'account_sid',
        'call_sid',
        'phone',
        'log',
        'type',
    ];
}
