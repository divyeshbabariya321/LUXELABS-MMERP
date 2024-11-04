<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class BingClientAccount extends Model
{
    protected $guarded = [];

    public function mails(): HasMany
    {
        return $this->hasMany(BingClientAccountMail::class, 'bing_client_account_id', 'id');
    }
}
