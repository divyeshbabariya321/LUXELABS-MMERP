<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsService extends Model
{
    protected $table = 'sms_service';

    protected $fillable = ['name'];
}
