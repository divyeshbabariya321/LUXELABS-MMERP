<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioTaskQueue extends Model
{
    protected $table = 'twilio_task_queue';

    protected $fillable = ['twilio_credential_id', 'twilio_workspace_id', 'task_order', 'task_queue_name', 'task_queue_sid',
        'reservation_activity_id', 'assignment_activity_id', 'target_workers', 'max_reserved_workers', 'deleted', ];
}
