<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationToken extends Model
{
    protected $fillable = [
        'user_id', 'device_token', 'is_enabled',
    ];
}
