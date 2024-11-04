<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushToMagentoCondition extends Model
{

    protected $fillable = [
        'condition',
        'description',
        'status',
        'upteam_status',
    ];
}
