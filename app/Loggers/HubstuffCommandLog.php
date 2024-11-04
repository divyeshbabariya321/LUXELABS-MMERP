<?php

namespace App\Loggers;
use App\Loggers\HubstuffCommandLogMessage;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class HubstuffCommandLog extends Model
{
    public function messages(): HasMany
    {
        return $this->hasMany(HubstuffCommandLogMessage::class);
    }
}
