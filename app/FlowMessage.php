<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowMessage extends Model
{
    protected $table = 'flow_action_messages';

    protected $fillable = [
        'action_id',
        'sender_name',
        'sender_email_address',
        'subject',
        'html_content',
        'reply_to_email',
        'sender_email_as_reply_to',
        'mail_tpl',
        'deleted',
    ];
}
