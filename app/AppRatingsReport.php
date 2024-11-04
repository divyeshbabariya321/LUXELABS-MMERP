<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppRatingsReport extends Model
{
    protected $table = 'ios_ratings_report';

    protected $fillable = ['networks', 'start_date', 'end_date', 'product_id', 'revenue', 'requests', 'impressions', 'ecpm', 'fillrate', 'ctr', 'clicks', 'requests_filled'];

    public $timestamps = false;
}
