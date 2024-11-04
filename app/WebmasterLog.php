<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebmasterLog extends Model
{
    protected $fillable = ['user_name', 'name', 'status', 'message'];
}
