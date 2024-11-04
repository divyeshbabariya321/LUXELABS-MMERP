<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskMessage extends Model
{
    protected $fillable = ['message_type', 'frequency', 'message'];
}
