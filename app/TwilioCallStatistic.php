<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioCallStatistic extends Model
{
    protected $guarded = [];

    const DIRECTION = [
        'inbound'  => 1,
        'outbound' => 2,
    ];
}
