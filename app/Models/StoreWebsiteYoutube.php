<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteYoutube extends Model
{

    protected $fillable = ['access_token', 'refresh_token', 'store_website_id', 'token_expire_time', 'created_at', 'updated_at', 'deleted_at'];
}
