<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CroppingInstance extends Model
{
    protected $fillable = [
        'instance_id',
        'comment',
    ];
}
