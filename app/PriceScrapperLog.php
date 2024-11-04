<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceScrapperLog extends Model
{
    protected $fillable = [
        'site_id',
        'link',
        'log_description',
    ];
}
