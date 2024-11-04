<?php

namespace App\Loggers;
use App\Loggers\TimeDoctorCommandLog;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class TimeDoctorCommandLogMessage extends Model
{
    protected $guarded = [];

    public function timeDoctorCommandLog(): BelongsTo
    {
        return $this->belongsTo(TimeDoctorCommandLog::class);
    }
}
