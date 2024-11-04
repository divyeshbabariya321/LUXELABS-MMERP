<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SlowLogsEnableDisable extends Model
{
    protected $fillable = ['user_id', 'response', 'type'];
}
