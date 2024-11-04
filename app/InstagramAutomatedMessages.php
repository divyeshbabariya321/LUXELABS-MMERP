<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class InstagramAutomatedMessages extends Model
{
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(Influencers::class, 'target_id', 'id');
    }
}
