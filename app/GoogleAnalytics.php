<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleAnalytics extends Model
{

    protected $fillable = ['website_analytics_id', 'dimensions', 'dimensions_name', 'dimensions_value'];
}
