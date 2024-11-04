<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class LearningDueDateHistory extends Model
{
    protected $table = 'learning_duedate_history';

    protected $fillable = [
        'learning_id',
        'old_duedate',
        'new_duedate',
        'update_by',
    ];

    public function learning(): BelongsTo
    {
        return $this->belongsTo(Learning::class, 'learning_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_by', 'id');
    }
}
