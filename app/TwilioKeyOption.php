<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioKeyOption extends Model
{

    protected $fillable = ['key', 'description', 'details', 'website_store_id', 'message'];
}
