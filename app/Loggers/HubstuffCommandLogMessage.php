<?php

namespace App\Loggers;
use App\Loggers\HubstuffCommandLog;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class HubstuffCommandLogMessage extends Model
{
    protected $guarded = [];

    public function hubstuffCommandLog(): BelongsTo
    {
        return $this->belongsTo(HubstuffCommandLog::class);
    }
}
