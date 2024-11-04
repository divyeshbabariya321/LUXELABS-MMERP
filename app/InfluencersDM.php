<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class InfluencersDM extends Model
{
    public function message(): BelongsTo
    {
        return $this->belongsTo(InstagramAutomatedMessages::class, 'message_id', 'id');
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencers::class, 'influencer_id', 'id');
    }
}
