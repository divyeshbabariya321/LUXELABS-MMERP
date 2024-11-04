<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NegativeCouponResponse extends Model
{
    protected $table = 'nagative_coupon_responses';

    protected $fillable = ['id', 'store_website_id', 'user_id', 'website', 'response', 'created_at', 'updated_at'];
}
