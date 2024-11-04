<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{

    protected $fillable = [
        'store_website_id',
        'flow_name',
        'flow_description',
        'flow_code',
    ];
}
