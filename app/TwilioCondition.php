<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TwilioConditionStatus;
use Illuminate\Database\Eloquent\Model;

class TwilioCondition extends Model
{

    protected $fillable = [
        'condition',
        'description',
        'status',
    ];

    public function twilioStatusColour(): BelongsTo
    {
        return $this->belongsTo(TwilioConditionStatus::class, 'status');
    }
}
