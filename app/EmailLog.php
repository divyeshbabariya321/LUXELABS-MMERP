<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    public const EMAIL_ALERT = 'email_alert';
    
    protected $fillable = ['email_id', 'email_log', 'message', 'is_error', 'service_type', 'source'];
}
