<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioAgent extends Model
{

    protected $fillable = ['user_id', 'status', 'store_website_id'];
}
