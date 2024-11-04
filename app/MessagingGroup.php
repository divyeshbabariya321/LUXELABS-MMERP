<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessagingGroup extends Model
{
    protected $fillable =
    [
        'name',
        'store_website_id',
        'service_id',
    ];
}
