<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Account;

class Post extends Model
{
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
