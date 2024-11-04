<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class TaskHistories extends Model
{
    protected $table = 'task_history';

    protected $fillable = [
        'user_id', 'task_subject_id', 'name_before', 'name_after', 'description_before', 'description_after',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(TaskSubject::class);
    }
}
