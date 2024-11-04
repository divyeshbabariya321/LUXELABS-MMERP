<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReplyUpdateHistory extends Model
{
    protected $fillable = ['id', 'reply_id', 'user_id', 'last_message'];
}
