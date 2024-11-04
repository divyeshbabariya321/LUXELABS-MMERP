<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Event;
use App\EventAlertLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventSchedule extends Model
{
    use HasFactory, SoftDeletes;

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get all of the eventSchedule's alert logs.
     */
    public function eventAlertLogs(): MorphMany
    {
        return $this->morphMany(EventAlertLog::class, 'eventalertloggable');
    }
}
