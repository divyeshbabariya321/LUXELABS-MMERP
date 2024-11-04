<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDeveloperStatusHistoryLog extends Model
{

    protected $fillable = ['user_id', 'uichecks_id', 'old_status_id', 'status_id', 'created_at'];
}
