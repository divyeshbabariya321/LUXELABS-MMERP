<?php

namespace App\UserEvent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use \App\UserEvent\UserEvent;
class UserEventAttendee extends Model
{
    protected $fillable = [
        'user_event_id',
        'contact',
        'suggested_time',
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
