<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZabbixTaskAssigneeHistory extends Model
{
    use HasFactory;

    public $fillable = [
        'zabbix_task_id',
        'old_assignee',
        'new_assignee',
        'user_id',
    ];

    public function zabbixTask(): BelongsTo
    {
        return $this->belongsTo(ZabbixTask::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newAssignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'new_assignee');
    }

    public function oldAssignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'old_assignee');
    }
}
