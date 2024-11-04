<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class GoogleClientAccount extends Model
{
    protected $guarded = [];

    public function mails(): HasMany
    {
        return $this->hasMany(GoogleClientAccountMail::class, 'google_client_account_id', 'id');
    }
}
