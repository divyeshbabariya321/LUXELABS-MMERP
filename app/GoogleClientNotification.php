<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class GoogleClientNotification extends Model
{
    protected $guarded = [];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'receiver_id');
    }

    public function account(): HasOne
    {
        return $this->hasOne(GoogleClientAccountMail::class, 'id', 'google_client_id');
    }
}
