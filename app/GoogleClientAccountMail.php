<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class GoogleClientAccountMail extends Model
{
    protected $guarded = [];

    public function google_client_account(): HasOne
    {
        return $this->hasOne(GoogleClientAccount::class, 'id', 'google_client_account_id');
    }
}
