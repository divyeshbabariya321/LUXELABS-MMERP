<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioMessageTone extends Model
{

    protected $fillable = ['store_website_id', 'end_work_ring', 'intro_ring', 'busy_ring', 'wait_url_ring'];
}
