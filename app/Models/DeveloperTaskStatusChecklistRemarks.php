<?php

namespace App\Models;
use App\User;
use App\Models;
use App\DeveloperTask;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class DeveloperTaskStatusChecklistRemarks extends Model
{
    protected $fillable = [
        'user_id',
        'task_id',
        'developer_task_status_checklist_id',
        'remark',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(DeveloperTask::class);
    }

    public function taskStatusChecklist(): BelongsTo
    {
        return $this->belongsTo(DeveloperTask::class);
    }
}
