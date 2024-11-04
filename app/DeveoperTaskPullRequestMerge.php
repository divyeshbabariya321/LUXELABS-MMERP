<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeveoperTaskPullRequestMerge extends Model
{
    protected $fillable = [
        'task_id',
        'pull_request_id',
    ];
}
