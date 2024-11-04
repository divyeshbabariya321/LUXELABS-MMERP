<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioError extends Model
{

    protected $fillable = ['sid', 'account_sid', 'call_sid', 'error_code', 'message_text', 'message_date', 'status'];
}
