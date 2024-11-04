<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class LearningStatusHistory extends Model
{
    protected $table = 'learning_status_history';

    protected $fillable = [
        'learning_id',
        'old_status',
        'new_status',
        'update_by',
    ];

    public function learning(): BelongsTo
    {
        return $this->belongsTo(Learning::class, 'learning_id', 'id');
    }

    public function oldstatus(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'old_status', 'id');
    }

    public function newstatus(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'new_status', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_by', 'id');
    }
}
