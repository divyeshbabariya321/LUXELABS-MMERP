<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogMessageStatus extends Model
{
    public $table = 'log_message_status';

    public $fillable = ['log_message', 'status'];
}
