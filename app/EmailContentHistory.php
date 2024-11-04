<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class EmailContentHistory extends Model
{
    protected $table = 'email_content_history';

    public function addedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}
