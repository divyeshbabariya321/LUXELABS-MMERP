<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BingWebmasterLog extends Model
{
    protected $fillable = ['user_name', 'name', 'status', 'message'];
}
