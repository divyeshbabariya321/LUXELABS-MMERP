<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CronJobErroLog extends Model
{

    protected $fillable = ['id', 'signature', 'priority', 'error', 'error_count', 'status', 'module', 'subject', 'assigned_to', 'updated_at', 'created_at'];
}
