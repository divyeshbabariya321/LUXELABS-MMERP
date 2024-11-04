<?php

namespace App\Marketing;

use Illuminate\Database\Eloquent\Model;

class WhatsappBusinessAccounts extends Model
{
    protected $fillable = [
        'about', 'address', 'description', 'email', 'profile_picture_url', 'websites',
        'business_phone_number', 'business_account_id', 'business_access_token', 'business_phone_number_id',
    ];
}
