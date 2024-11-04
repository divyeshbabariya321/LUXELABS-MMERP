<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioSitewiseTime extends Model
{

    protected $fillable = ['store_website_id', 'start_time', 'end_time'];
}
