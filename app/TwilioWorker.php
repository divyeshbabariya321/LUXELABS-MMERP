<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\User;

class TwilioWorker extends Model
{

    protected $fillable = ['twilio_credential_id', 'twilio_workspace_id', 'user_id', 'priority', 'worker_name', 'worker_sid', 'twilio_workers', 'deleted'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
