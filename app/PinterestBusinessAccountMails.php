<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class PinterestBusinessAccountMails extends Model
{
    protected $fillable = [
        'pinterest_account',
        'pinterest_business_account_id',
        'pinterest_refresh_token',
        'pinterest_access_token',
        'expires_in',
        'refresh_token_expires_in',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(PinterestBusinessAccounts::class, 'id', 'pinterest_business_account_id');
    }
}
