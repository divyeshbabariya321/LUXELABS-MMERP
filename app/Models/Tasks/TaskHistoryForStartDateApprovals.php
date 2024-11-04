<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\User;

class TaskHistoryForStartDateApprovals extends Model
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
