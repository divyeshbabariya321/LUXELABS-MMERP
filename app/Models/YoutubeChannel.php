<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeChannel extends Model
{
    protected $fillable = [
        'access_token',
        'oauth2_refresh_token',
        'store_websites',
        'chanel_name',
        'status',
        'oauth2_client_id',
        'oauth2_client_secret',
        'email',
        'token_expire_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
