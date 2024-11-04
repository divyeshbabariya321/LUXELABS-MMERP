<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RedisQueue extends Model
{

    protected $fillable = [
        'name', 'type',
    ];
}
