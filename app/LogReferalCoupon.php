<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogReferalCoupon extends Model
{
    protected $fillable = ['refer_friend_id', 'log', 'message'];
}
