<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Email;

/**
 * Class SendgridEvent
 *
 *
 * @property array|string[] $categories
 * @property Carbon         $created_at
 * @property string         $email
 * @property string         $event
 * @property int            $id
 * @property string         $sg_event_id
 * @property string         $sg_message_id
 * @property array          $payload
 * @property Carbon         $timestamp
 * @property Carbon         $updated_at
 */
class SendgridEvent extends Model
{
    protected $fillable = ['timestamp', 'email', 'event', 'sg_event_id', 'sg_message_id', 'categories', 'payload', 'email_id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'timestamp'  => 'datetime',
            'payload'    => 'array',
            'categories' => 'array',
        ];
    }

    /**
     * Get the current connection name for the model.
     */
    public function getConnectionName(): ?string
    {
        return config('sendgridevents.database_connection_for_events');
    }

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        return config('sendgridevents.events_table_name');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Email::class, 'email_id');
    }

    public function getEventColorAttribute()
    {
        $eventColor = SendgridEventColor::where('name', $this->event)->first();

        if ($eventColor) {
            return $eventColor->color;
        }

        return '';
    }
}
