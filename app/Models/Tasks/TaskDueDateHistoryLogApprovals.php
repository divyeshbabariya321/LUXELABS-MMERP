<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\User;

class TaskDueDateHistoryLogApprovals extends Model
{
    public $table = 'task_due_date_history_logs_approvals';

    public $fillable = [
        'parent_id',
        'approved_by',
    ];

    public function approvedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'approved_by');
    }

    public function approvedByName()
    {
        return $this->approvedBy?->name;
    }
}
