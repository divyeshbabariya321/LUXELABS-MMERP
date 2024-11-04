<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserFeedbackCategorySopHistory extends Model
{
    protected $fillable = [
        'id', 'user_id', 'vendor_id', 'category_id', 'sop', 'sops_id', 'created_at', 'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
