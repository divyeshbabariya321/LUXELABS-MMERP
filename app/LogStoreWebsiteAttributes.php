<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogStoreWebsiteAttributes extends Model
{
    protected $fillable = [
        'log_case_id',
        'attribute_id',
        'attribute_key',
        'attribute_val',
        'store_website_id',
        'log_msg',
    ];
}
