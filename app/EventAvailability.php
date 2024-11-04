<?php

namespace App;
use App\Event;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EventAvailability extends Model
{
    protected $fillable = [
        'event_id',
        'numeric_day',
        'start_at',
        'end_at',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
