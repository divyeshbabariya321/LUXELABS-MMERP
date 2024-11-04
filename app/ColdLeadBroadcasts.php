<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ColdLeadBroadcasts extends Model
{
    public function lead(): BelongsToMany
    {
        return $this->belongsToMany(ColdLeads::class, 'lead_broadcasts_lead', 'lead_broadcast_id', 'lead_id', 'id', 'id');
    }

    public function imQueueBroadcast(): HasMany
    {
        return $this->hasMany(ImQueue::class, 'broadcast_id', 'id');
    }

    public function imQueueBroadcastPending(): HasMany
    {
        return $this->hasMany(ImQueue::class, 'broadcast_id', 'id')->whereNull('sent_at');
    }

    public function imQueueBroadcastSend(): HasMany
    {
        return $this->hasMany(ImQueue::class, 'broadcast_id', 'id')->whereNotNull('sent_at');
    }
}
