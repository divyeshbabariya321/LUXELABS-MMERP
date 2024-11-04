<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowType extends Model
{

    protected $fillable = [
        'type',
        'deleted',
    ];
}
