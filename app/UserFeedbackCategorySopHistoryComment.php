<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserFeedbackCategorySopHistoryComment extends Model
{
    protected $fillable = [
        'id', 'user_id', 'sop_history_id', 'comment', 'created_at', 'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
