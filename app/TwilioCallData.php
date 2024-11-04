<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioCallData extends Model
{
    protected $table = 'twilio_call_data';

    protected $fillable = ['call_sid', 'account_sid', 'from', 'to', 'aget_user_id', 'call_data'];
}
