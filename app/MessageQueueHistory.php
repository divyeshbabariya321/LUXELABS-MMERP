<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MessageQueueHistory extends Model
{
    protected $table = 'message_queue_history';

    protected $fillable = [
        'number',
        'counter',
        'type',
        'user_id',
        'time',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
