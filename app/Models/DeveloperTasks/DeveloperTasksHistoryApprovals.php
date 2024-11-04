<?php

namespace App\Models\DeveloperTasks;
use App\User;
use App\Models\DeveloperTasks;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class DeveloperTasksHistoryApprovals extends Model
{

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
