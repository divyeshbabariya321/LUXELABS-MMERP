<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateMarketingLogs extends Model
{
    protected $fillable = ['user_name', 'name', 'status', 'message'];
}
