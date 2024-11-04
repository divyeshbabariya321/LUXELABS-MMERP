<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserFeedbackCategory extends Model
{
    protected $fillable = [
        'user_id', 'category',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
