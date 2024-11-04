<?php

namespace App\UserEvent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use \App\UserEvent\UserEvent;
class UserEventParticipant extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'object',
        'object_id',
        'user_event_id',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(
            UserEvent::class,
            'user_event_id',
            'id'
        );
    }
}
