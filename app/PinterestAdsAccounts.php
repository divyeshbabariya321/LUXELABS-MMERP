<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class PinterestAdsAccounts extends Model
{
    protected $fillable = [
        'pinterest_mail_id',
        'ads_account_id',
        'ads_account_name',
        'ads_account_country',
        'ads_account_currency',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(PinterestBusinessAccountMails::class, 'id', 'pinterest_mail_id');
    }
}
