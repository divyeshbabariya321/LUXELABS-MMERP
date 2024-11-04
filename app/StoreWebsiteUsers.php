<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteUsers extends Model
{

    protected $fillable = [
        'store_website_id', 'email', 'password', 'username', 'first_name', 'last_name', 'website_mode', 'is_active', 'user_role', 'user_role_name', 'request_data', 'response_data',
    ];
}
