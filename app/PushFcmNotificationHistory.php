<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushFcmNotificationHistory extends Model
{

    protected $primaryKey = 'id';

    protected $fillable = ['id', 'token', 'notification_id', 'success', 'error_message'];
}
