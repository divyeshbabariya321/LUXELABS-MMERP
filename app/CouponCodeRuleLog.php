<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CouponCodeRuleLog extends Model
{
    protected $fillable = ['rule_id', 'coupon_code', 'log_type', 'message'];
}
