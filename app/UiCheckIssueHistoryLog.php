<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiCheckIssueHistoryLog extends Model
{

    protected $fillable = ['id', 'user_id', 'uichecks_id', 'old_issue', 'issue', 'created_at'];
}
