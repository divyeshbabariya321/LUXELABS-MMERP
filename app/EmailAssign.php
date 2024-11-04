<?php

namespace App;
use App\User;
use App\EmailAddress;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class EmailAssign extends Model
{
    protected $table = 'email_assignes';

    protected $fillable = [
        'email_address_id',
        'user_id',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function emailAddress(): HasOne
    {
        return $this->hasOne(EmailAddress::class, 'id', 'email_address_id');
    }
}
