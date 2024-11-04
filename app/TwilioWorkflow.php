<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioWorkflow extends Model
{

    protected $fillable = [
        'twilio_credential_id',
        'twilio_workspace_id',
        'workflow_name',
        'workflow_sid',
        'deleted',
        'fallback_assignment_callback_url',
        'assignment_callback_url',
        'task_timeout',
        'worker_reservation_timeout',
    ];
}
