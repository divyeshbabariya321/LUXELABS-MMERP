<?php

namespace App\Loggers;
use App\Loggers\TimeDoctorCommandLogMessage;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class TimeDoctorCommandLog extends Model
{
    public function messages(): HasMany
    {
        return $this->hasMany(TimeDoctorCommandLogMessage::class);
    }
}
